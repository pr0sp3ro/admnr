<?php

/** Copies query results as Markdown for AI (query, header, rows, execution time)
* @link https://www.adminer.org/plugins/#use
* @author Miroslav
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerAiMdExport extends Adminer\Plugin {

	protected $translations = array(
		'cs' => array(
			'' => 'Zkopíruje výsledky dotazů jako Markdown pro AI',
			'AI Export' => 'Export pro AI',
			'Copy .md' => 'Kopírovat .md',
			'Copied' => 'Zkopírováno',
			'Save .md' => 'Uložit .md',
			'Saved' => 'Uloženo',
			'Failed' => 'Nepovedlo se',
			'SQL Results' => 'Výsledky SQL',
			'Query' => 'Dotaz',
			'Result' => 'Výsledek',
			'Error' => 'Chyba',
			'Warning' => 'Varování',
			'rows' => 'řádků',
			'query' => 'dotaz',
			'queries' => array('dotaz', 'dotazy', 'dotazů'),
			'Database' => 'Databáze',
			'Table' => 'Tabulka',
			'Exported' => 'Exportováno',
			'Queries' => 'Počet dotazů',
			'Total' => 'Celkem',
		),
		'it' => array(
			'' => 'Copia i risultati delle query come Markdown per AI',
			'AI Export' => 'Esporta per AI',
			'Copy .md' => 'Copia .md',
			'Copied' => 'Copiato',
			'Save .md' => 'Salva .md',
			'Saved' => 'Salvato',
			'Failed' => 'Fallito',
			'SQL Results' => 'Risultati SQL',
			'Result' => 'Risultato',
			'Error' => 'Errore',
			'Warning' => 'Avviso',
			'rows' => 'righe',
			'queries' => 'query',
			'Table' => 'Tabella',
			'Exported' => 'Esportato',
			'Queries' => 'Query',
			'Total' => 'Totale',
		),
	);

	function head($dark = null) {
		// English is the identifier itself, other languages come from $translations via Adminer\LANG (works in single-language builds too)
		$T = array(
			'bar' => $this->lang('AI Export'),
			'copy' => $this->lang('Copy .md'),
			'copied' => $this->lang('Copied'),
			'save' => $this->lang('Save .md'),
			'saved' => $this->lang('Saved'),
			'failed' => $this->lang('Failed'),
			'title' => $this->lang('SQL Results'),
			'query' => $this->lang('Query'),
			'result' => $this->lang('Result'),
			'error' => $this->lang('Error'),
			'warning' => $this->lang('Warning'),
			'rows' => $this->lang('rows'),
			'plural_one' => $this->lang('query'),
			'plural_few' => $this->lang('queries', 2),
			'plural_many' => $this->lang('queries', 5),
			'server' => $this->lang('Server'),
			'database' => $this->lang('Database'),
			'table' => $this->lang('Table'),
			'exported' => $this->lang('Exported'),
			'queries' => $this->lang('Queries'),
			'total' => $this->lang('Total'),
		);
		?>
<style>
#ai-md-export {
	display: flex;
	align-items: center;
	flex-wrap: wrap;
	gap: .5em;
	margin: .75em 0;
	padding: .55em .8em;
	border: 1px solid #c7d2fe;
	border-left: 4px solid #4f46e5;
	border-radius: 6px;
	background: linear-gradient(180deg, #eef2ff, #e0e7ff);
	box-shadow: 0 1px 3px rgba(49, 46, 129, .12);
	font-size: 13px;
	line-height: 1.2;
}
#ai-md-export .ai-md-label {
	display: inline-flex;
	align-items: center;
	gap: .35em;
	margin-right: .25em;
	color: #3730a3;
	font-weight: bold;
	letter-spacing: .02em;
}
#ai-md-export .ai-md-label svg {
	width: 17px;
	height: 17px;
	color: #6366f1;
}
#ai-md-export .ai-md-btn {
	display: inline-flex;
	align-items: center;
	gap: .4em;
	margin: 0;
	padding: .45em .85em;
	border: 1px solid #c7d2fe;
	border-radius: 5px;
	background: #fff;
	color: #3730a3;
	font: inherit;
	font-weight: bold;
	cursor: pointer;
	transition: background .15s, border-color .15s, color .15s, box-shadow .15s;
}
#ai-md-export .ai-md-btn:hover {
	border-color: #4f46e5;
	background: #f5f3ff;
	color: #312e81;
}
#ai-md-export .ai-md-btn:active {
	transform: translateY(1px);
}
#ai-md-export .ai-md-btn:focus-visible {
	outline: 2px solid #4f46e5;
	outline-offset: 2px;
}
#ai-md-export .ai-md-primary {
	border-color: #4338ca;
	background: #4f46e5;
	color: #fff;
	box-shadow: 0 1px 2px rgba(49, 46, 129, .3);
}
#ai-md-export .ai-md-primary:hover {
	border-color: #3730a3;
	background: #4338ca;
	color: #fff;
}
#ai-md-export .ai-md-ok,
#ai-md-export .ai-md-ok:hover {
	border-color: #047857;
	background: #059669;
	color: #fff;
}
#ai-md-export .ai-md-fail,
#ai-md-export .ai-md-fail:hover {
	border-color: #b91c1c;
	background: #dc2626;
	color: #fff;
}
#ai-md-export .ai-md-count {
	margin-left: auto;
	padding: .25em .6em;
	border: 1px solid #c7d2fe;
	border-radius: 999px;
	background: rgba(255, 255, 255, .7);
	color: #4338ca;
	font-size: .92em;
}
#ai-md-export svg {
	width: 15px;
	height: 15px;
	flex: none;
}
@media (prefers-color-scheme: dark) {
	/* uplatni se jen kdyz Adminer nevnucuje svetly rezim (viz meta color-scheme) */
	html:not(.ai-md-light) #ai-md-export {
		border-color: #3730a3;
		background: linear-gradient(180deg, #1e1b4b, #172554);
	}
	html:not(.ai-md-light) #ai-md-export .ai-md-label {
		color: #c7d2fe;
	}
	html:not(.ai-md-light) #ai-md-export .ai-md-btn {
		border-color: #4338ca;
		background: #312e81;
		color: #e0e7ff;
	}
	html:not(.ai-md-light) #ai-md-export .ai-md-btn:hover {
		background: #3730a3;
		color: #fff;
	}
	html:not(.ai-md-light) #ai-md-export .ai-md-primary {
		background: #4f46e5;
		color: #fff;
	}
	html:not(.ai-md-light) #ai-md-export .ai-md-count {
		border-color: #4338ca;
		background: rgba(15, 23, 42, .5);
		color: #c7d2fe;
	}
}
</style>
<script <?php echo Adminer\nonce(); ?>>
(function () {
	'use strict';

	const T = <?php echo json_encode($T, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>;

	const ICON = {
		spark: '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M10 2c.6 4.4 2.8 6.6 7.2 7.2-4.4.6-6.6 2.8-7.2 7.2-.6-4.4-2.8-6.6-7.2-7.2C7.2 8.6 9.4 6.4 10 2z"/><path d="M18 14c.3 2.4 1.3 3.4 3.7 3.7-2.4.3-3.4 1.3-3.7 3.7-.3-2.4-1.3-3.4-3.7-3.7 2.4-.3 3.4-1.3 3.7-3.7z"/></svg>',
		copy: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>',
		save: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
		ok: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>',
		fail: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
	};

	function txt(node) {
		return (node.textContent || '').replace(/ /g, ' ');
	}

	function squash(s) {
		return s.replace(/\s+/g, ' ').trim();
	}

	function plural(n, one, few, many) {
		return n === 1 ? one : (n >= 2 && n <= 4 ? few : many);
	}

	/** Text bunky pripraveny pro Markdown tabulku */
	function cellText(el) {
		const clone = el.cloneNode(true);
		clone.querySelectorAll('span.column, input').forEach(function (n) {
			n.remove(); // ovladaci prvky hlavicky na strance Vypsat data
		});
		clone.querySelectorAll('a').forEach(function (a) {
			if (a.textContent.trim() === '?') {
				a.remove(); // odkaz do dokumentace u EXPLAIN
			}
		});
		let s = squash(txt(clone)).replace(/\|/g, '\\|');
		if (/\\$/.test(s)) {
			s += ' '; // aby koncove zpetne lomitko neuteklo oddelovac sloupcu
		}
		return s;
	}

	/** Bunky radku bez sloupce se zaskrtavatky */
	function cellsOf(tr) {
		return Array.prototype.slice.call(tr.cells).filter(function (c) {
			return !c.classList.contains('check');
		});
	}

	function tableToMd(table) {
		const rows = Array.prototype.slice.call(table.rows);
		if (!rows.length) {
			return '';
		}
		const header = cellsOf(rows[0]);
		const cols = header.length;
		if (!cols) {
			return '';
		}
		const body = rows.slice(1).map(cellsOf);
		const align = [];
		for (let i = 0; i < cols; i++) {
			const numeric = body.length && body.every(function (cs) {
				return cs[i] && cs[i].classList.contains('number');
			});
			align.push(numeric ? '---:' : '---');
		}
		const line = function (arr) {
			const a = arr.slice(0, cols);
			while (a.length < cols) {
				a.push('');
			}
			return '| ' + a.join(' | ') + ' |';
		};
		return [line(header.map(cellText)), line(align)]
			.concat(body.map(function (cs) {
				return line(cs.map(cellText));
			}))
			.join('\n');
	}

	/** Text uzlu az po prvni <span class="time"> */
	function textBeforeTime(el) {
		let s = '';
		for (const n of Array.prototype.slice.call(el.childNodes)) {
			if (n.nodeType === 1 && n.classList && n.classList.contains('time')) {
				break;
			}
			s += txt(n);
		}
		return squash(s).replace(/[,;\s]+$/, '');
	}

	function timeOf(el) {
		const t = el.querySelector('span.time');
		return t ? squash(txt(t)) : '';
	}

	/** Uplne zneni dotazu z odkazu "Upravit" (v <pre> je zkraceny na 1000 znaku) */
	function sqlFromLink(scope) {
		const a = scope && scope.querySelector('a[href*="sql="]');
		if (!a) {
			return '';
		}
		const m = /[?&]sql=([^&]*)/.exec(a.getAttribute('href') || '');
		if (!m || !m[1]) {
			return '';
		}
		try {
			return decodeURIComponent(m[1].replace(/\+/g, ' '));
		} catch (e) {
			return '';
		}
	}

	/** Stranka "SQL prikaz": rozseka vysledky na bloky podle jednotlivych dotazu */
	function collectSql() {
		const first = document.querySelector('pre[id^="sql-"]');
		if (!first) {
			return [];
		}
		const blocks = [];
		let b = null;
		for (const el of Array.prototype.slice.call(first.parentNode.children)) {
			if (el.tagName === 'PRE' && /^sql-\d+$/.test(el.id)) {
				b = {
					sql: txt(el.querySelector('code') || el).trim(),
					fullSql: '',
					rows: '',
					time: '',
					items: [],
				};
				blocks.push(b);
				continue;
			}
			if (!b) {
				continue;
			}
			if (el.id === 'form') {
				break; // spodni formular s textareou
			}
			if (el.tagName === 'FORM') {
				const f = el.querySelector('p.sql-footer');
				if (f) {
					b.rows = textBeforeTime(f);
					b.time = b.time || timeOf(f);
					// skryte pole formulare Export nese cely dotaz, <pre> je zkracene na 1000 znaku
					const q = el.querySelector('input[name="query"]');
					b.fullSql = (q && q.value) || b.fullSql || sqlFromLink(f);
				}
				continue;
			}
			if (el.classList.contains('explain')) {
				continue; // automaticky EXPLAIN je skryty a pro AI jen sum; rucni "EXPLAIN SELECT" je bezny vysledek
			}
			if (el.classList.contains('scrollable')) {
				const t = el.querySelector('table');
				if (t) {
					b.items.push({type: 'table', md: tableToMd(t)});
				}
				continue;
			}
			if (/^warnings-\d+$/.test(el.id || '')) {
				b.items.push({type: 'warning', text: squash(txt(el))});
				continue;
			}
			if (el.tagName === 'P' && el.classList.contains('error')) {
				// zaverecne shrnuti "Chyba v dotazu: 5" jen odkazuje na uz vypsane chyby
				if (!el.querySelector('a[href^="#sql-"]')) {
					b.items.push({type: 'error', text: squash(txt(el))});
				}
				continue;
			}
			if (el.tagName === 'P' && el.classList.contains('message')) {
				b.items.push({type: 'message', text: textBeforeTime(el)});
				b.time = b.time || timeOf(el);
				b.fullSql = b.fullSql || sqlFromLink(el);
				continue;
			}
		}
		return blocks;
	}

	/** Stranka "Vypsat data": jedna tabulka + dotaz, ktery ji vygeneroval */
	function collectSelect() {
		const table = document.getElementById('table');
		if (!table) {
			return [];
		}
		let head = null;
		const ps = document.querySelectorAll('#content p');
		for (const p of Array.prototype.slice.call(ps)) {
			if (p.querySelector('code[class*="jush-"]') && p.querySelector('span.time')) {
				head = p;
				break;
			}
		}
		const b = {
			sql: head ? squash(txt(head.querySelector('code'))) : '',
			fullSql: head ? sqlFromLink(head) : '',
			rows: '',
			time: head ? timeOf(head) : '',
			items: [{type: 'table', md: tableToMd(table)}],
		};
		const all = document.querySelector('.footer input[name="all"]');
		if (all && all.parentNode) {
			b.rows = squash(txt(all.parentNode));
		} else {
			b.rows = Math.max(0, table.rows.length - 1) + ' ' + T.rows;
		}
		return [b];
	}

	function pad(n) {
		return String(n).padStart(2, '0');
	}

	function stamp() {
		const d = new Date();
		return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate())
			+ ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
	}

	function param(name) {
		return new URLSearchParams(location.search).get(name) || '';
	}

	function blocks() {
		const sql = collectSql();
		return sql.length ? sql : collectSelect();
	}

	function buildMd() {
		const list = blocks();
		if (!list.length) {
			return '';
		}
		const db = param('db');
		const table = param('select');
		const crumb = document.querySelector('#breadcrumb a');
		const server = crumb ? squash(txt(crumb)) : '';
		const out = [];

		out.push('# ' + T.title + (db ? ' - ' + db : ''));
		out.push('');
		if (server) {
			out.push('- ' + T.server + ': ' + server);
		}
		if (db) {
			out.push('- ' + T.database + ': `' + db + '`');
		}
		if (table) {
			out.push('- ' + T.table + ': `' + table + '`');
		}
		out.push('- ' + T.exported + ': ' + stamp());
		if (list.length > 1) {
			out.push('- ' + T.queries + ': ' + list.length);
			let total = 0;
			const ok = list.every(function (b) {
				const m = /([\d.]+)\s*s/.exec(b.time || '');
				if (!m) {
					return false;
				}
				total += parseFloat(m[1]);
				return true;
			});
			if (ok) {
				out.push('- ' + T.total + ': ' + total.toFixed(3) + ' s');
			}
		}

		list.forEach(function (b, idx) {
			const msgs = [];
			const errs = [];
			const warns = [];
			const tables = [];
			b.items.forEach(function (i) {
				if (i.type === 'message') {
					msgs.push(i.text);
				} else if (i.type === 'error') {
					errs.push(i.text);
				} else if (i.type === 'warning') {
					warns.push(i.text);
				} else if (i.type === 'table' && i.md) {
					tables.push(i.md);
				}
			});

			out.push('');
			out.push('---');
			out.push('');
			out.push('## ' + T.query + (list.length > 1 ? ' ' + (idx + 1) : ''));
			out.push('');
			out.push('```sql');
			out.push((b.fullSql || b.sql || '').trim());
			out.push('```');
			out.push('');

			const status = [b.rows].concat(msgs).concat([b.time]).filter(Boolean).join(' ');
			if (status) {
				out.push('**' + T.result + ':** ' + status);
				out.push('');
			}
			errs.forEach(function (e) {
				// Adminer always renders "<prefix> (<errno>): <message>", the prefix is localized so strip it language-independently
				const text = e.replace(/^[^:(]*(\(\d+\))?:\s*/, function (m0, code) {
					return code ? code + ' ' : '';
				});
				out.push('**' + T.error + ':** ' + text);
				out.push('');
			});
			warns.forEach(function (w) {
				out.push('**' + T.warning + ':** ' + w);
				out.push('');
			});
			tables.forEach(function (t) {
				out.push(t);
				out.push('');
			});
		});

		return out.join('\n').replace(/\n{3,}/g, '\n\n').replace(/\s+$/, '') + '\n';
	}

	function copyText(text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text);
		}
		return new Promise(function (resolve, reject) {
			const ta = document.createElement('textarea');
			ta.value = text;
			ta.setAttribute('readonly', '');
			ta.style.cssText = 'position:fixed;top:0;left:-9999px;';
			document.body.appendChild(ta);
			ta.select();
			ta.setSelectionRange(0, ta.value.length);
			let ok = false;
			try {
				ok = document.execCommand('copy');
			} catch (e) {
				ok = false;
			}
			ta.remove();
			ok ? resolve() : reject(new Error('copy failed'));
		});
	}

	function saveFile(text) {
		const name = 'adminer' + (param('db') ? '-' + param('db') : '')
			+ '-' + stamp().replace(/[: ]/g, '-') + '.md';
		const url = URL.createObjectURL(new Blob([text], {type: 'text/markdown;charset=utf-8'}));
		const a = document.createElement('a');
		a.href = url;
		a.download = name;
		document.body.appendChild(a);
		a.click();
		setTimeout(function () {
			URL.revokeObjectURL(url);
			a.remove();
		}, 0);
	}

	function setFace(btn, icon, label) {
		btn.innerHTML = icon;
		btn.appendChild(document.createTextNode(label));
	}

	function flash(btn, icon, label, cls) {
		setFace(btn, icon, label);
		btn.classList.add(cls);
		clearTimeout(btn.flashTimer);
		btn.flashTimer = setTimeout(function () {
			btn.classList.remove('ai-md-ok', 'ai-md-fail');
			setFace(btn, ICON[btn.dataset.icon], btn.dataset.label);
		}, 1600);
	}

	function button(iconName, label, primary, onClick) {
		const b = document.createElement('button');
		b.type = 'button';
		b.className = 'ai-md-btn' + (primary ? ' ai-md-primary' : '');
		b.dataset.icon = iconName;
		b.dataset.label = label;
		b.title = label;
		setFace(b, ICON[iconName], label);
		b.addEventListener('click', onClick);
		return b;
	}

	function init() {
		const list = blocks();
		if (!list.length) {
			return;
		}

		// Adminer vnucuje svetly rezim pres <meta name="color-scheme">, tmave styly pak musi ustoupit
		const scheme = document.querySelector('meta[name="color-scheme"]');
		if (scheme && /^\s*light\s*$/.test(scheme.content || '')) {
			document.documentElement.classList.add('ai-md-light');
		}

		const btnCopy = button('copy', T.copy, true, function () {
			copyText(buildMd()).then(function () {
				flash(btnCopy, ICON.ok, T.copied, 'ai-md-ok');
			}, function () {
				flash(btnCopy, ICON.fail, T.failed, 'ai-md-fail');
			});
		});
		const btnSave = button('save', T.save, false, function () {
			saveFile(buildMd());
			flash(btnSave, ICON.ok, T.saved, 'ai-md-ok');
		});

		const bar = document.createElement('div');
		bar.id = 'ai-md-export';

		const label = document.createElement('span');
		label.className = 'ai-md-label';
		label.innerHTML = ICON.spark;
		label.appendChild(document.createTextNode(T.bar));

		const count = document.createElement('span');
		count.className = 'ai-md-count';
		count.textContent = list.length + ' ' + plural(list.length, T.plural_one, T.plural_few, T.plural_many);

		bar.appendChild(label);
		bar.appendChild(btnCopy);
		bar.appendChild(btnSave);
		bar.appendChild(count);

		const h2 = document.querySelector('#content h2');
		if (h2) {
			h2.parentNode.insertBefore(bar, h2.nextSibling);
		} else {
			const first = document.querySelector('pre[id^="sql-"]') || document.getElementById('table');
			first.parentNode.insertBefore(bar, first);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
</script>
<?php
	}
}