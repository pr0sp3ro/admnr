<?php
/**
 * Autocompletion for search plugin
 * ================================
 * Delivers Excel-like behaviour when filtering tables using the search-fields. 
 * After selecting a column for search using the drop-down, all values in this column are fetched using AJAX and can be selected directly in the search text-field.
 * When one or more columns are already set for search then these values will be respected when autocomplete values are fetched from the database. This way, the search is refined with each selected column. 
 * 
 * Example: 
 *  - column `lastName` is selected with the value `Smith`
 *  - the user selects column `firstName`
 *  => only first names of all Smiths will be given for autocomplete 
 *
 * Written in vanilla JavaScript, no jquery needed.
 * 
 * Tested with Adminer 4.8.1 in PHP8.1 on MySQL in FireFox 104  and >5000 distinct values within one column without any performance degredation.
 * 
 * NOTE: Will fetch values with line feeds as well but those will be converted by the browser to single lines due to the features of the input field. So the search won't find the values in these cases. This is not this plugin's fault.
 *
 * @author Stephan Herrmann, https://github.com/derStephan/AdminerPlugins
 * @license MIT
 *
 */
class searchAutocomplete
{	
    public function afterConnect()
    {
        if (!isset($_POST['getAutoCompleteData'])) {
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        if (!Adminer\verify_token()) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token.']);
            exit;
        }

        $table  = (string) ($_GET['select'] ?? '');
        $column = (string) $_POST['getAutoCompleteData'];
        $fields = Adminer\fields($table);

        if (!isset($fields[$column])) {
            http_response_code(400);
            echo json_encode([]);
            exit;
        }

        set_time_limit(5);
        $_GET['where'] = (array) ($_POST['where'] ?? []);
        $where = Adminer\adminer()->selectSearchProcess($fields, Adminer\indexes($table));
        $field = Adminer\idf_escape($column);
        $query = 'SELECT DISTINCT ' . $field . ' FROM ' . Adminer\table($table);
        $query = Adminer\limit($query, ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY ' . $field, 1000);

        echo json_encode(Adminer\get_vals($query), JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }

	public function head($dark = null)
	{
		//all of this is only applicable in the simple table view
		if(!isset($_GET["select"]))
			return;
	?>
		<script <?php echo Adminer\nonce()?> type='text/javascript'>
		//prepare autocomplete searchFields on page load
		document.addEventListener('DOMContentLoaded', function()
		{
            const search = document.getElementById('fieldset-search');
            if (!search) return;
            search.addEventListener('mousedown', function(e) {
                if (e.target.matches('input[name$="[val]"]')) {
                    e.target.autocomplete = 'off';
                    populateAutocompleteDataList(e);
                }
            });
            search.addEventListener('change', function(e) {
                if (e.target.matches('select[name$="[col]"]')) {
                    populateAutocompleteDataList(e);
                }
            });
		});
		
		function populateAutocompleteDataList(e)
		{
			//column to be searched
			var column="";
			
			//called from text field
			if(e.type=="mousedown")
				column=e.target.parentElement.querySelector('select[name$="[col]"]').value;
			//called from drop down
			if(e.type=="change")
				column=e.target.value;			

			//no column selected, stop.
			if(column=="")
				return;
			
			//create datalist object if it does not exist
			if(!document.getElementById("autocompleteSource"))
			{
				//add datalist to searchfieldset. 
				var dataList = document.createElement("datalist");
				dataList.setAttribute("id", "autocompleteSource");
				document.getElementById("fieldset-search").appendChild(dataList);
			}
			var dataList=document.getElementById("autocompleteSource");
			
			//get all values of all other search fields for refined search
			//this way we only see valid options in auto complete
			var searchData="";
			
			//walk through all search text fields
			var searchFieldInputs=document.querySelectorAll('#fieldset-search input[name$="[val]"]');
			let whereCount=0;
			for (let searchFieldInput of searchFieldInputs )
			{	
				
				//unbind datalist from all search text fields
				searchFieldInput.setAttribute("list", "");
				
				//get all information about the search
				var searchFieldColumn=encodeURIComponent(searchFieldInput.parentElement.querySelector('select[name$="[col]"]').value);
				var searchFieldOperation=encodeURIComponent(searchFieldInput.parentElement.querySelector('select[name$="[op]"]').value);
				var searchFieldValue=encodeURIComponent(searchFieldInput.value);

				//include in refined search only if the column is set
				if(column != searchFieldColumn && searchFieldColumn != "")
				{
					//build string for ajax request. This cannot be sent as an array, so I don't even bother.
					searchData+="where["+whereCount+"][col]="+searchFieldColumn+"&where["+whereCount+"][op]="+searchFieldOperation+"&where["+whereCount+"][val]="+searchFieldValue+"&";
					whereCount++;
				}
				
			}

			
			// clear any previously loaded options in the datalist
			dataList.innerHTML = "";
			
			//get all values in the column using ajax
			var autoCompleteXHR = new XMLHttpRequest();
			autoCompleteXHR.open("POST", "", true);
			autoCompleteXHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			//submit
			autoCompleteXHR.send("getAutoCompleteData=" + encodeURIComponent(column) + "&token=" + encodeURIComponent(document.querySelector('input[name=token]').value) + "&" + searchData );
			autoCompleteXHR.onreadystatechange = function() 
			{
				if (this.readyState == 4 && this.status == 200) 
				{
					 // We're expecting a json response so we convert it to an object
					var response = JSON.parse( this.responseText ); 

					// clear any previously loaded options in the datalist
					dataList.innerHTML = "";

					response.forEach(function(item) 
					{
						// Create a new <option> element.
						var option = document.createElement('option');
						option.value = item;

						// attach the option to the datalist element
						dataList.appendChild(option);
					});
				}
			}
			
			//bind the datalist to the text field. 
			
			//if this event is fired from search text field
			if(e.type=="mousedown")
			{
				e.target.setAttribute("list", "autocompleteSource");
				e.target.focus();
			}
			//if this event is fired from drop down
			if(e.type=="change")
			{
				e.target.parentElement.querySelector('input[name$="[val]"]').blur();
				e.target.parentElement.querySelector('input[name$="[val]"]').setAttribute("list", "autocompleteSource");
				e.target.parentElement.querySelector('input[name$="[val]"]').focus();
			}
		}
		</script>
	<?php
	}
}
