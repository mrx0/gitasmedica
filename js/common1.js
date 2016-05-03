
 /* Common Lib (from http://ir2.ru/common.js) -utf */
document.write('<style type="text/css">#unjs {display:none;}</style>')

var hiddclass = 'disnone', disnone = 'disnone';

var Validator = {
	
	date1: {
		func: function(s){
		s = trim(s)
		return s.match(/^\d\d\.\d\d\.\d\d\d\d$/)
		},
		msg: 'Обязательное поле, дата (dd.mm.yyyy)' 
	},
	
	text: {
		func: function(s){
		s = trim(s)
		return s.length > 5 && s.match(/[а-яёЁ]/i)
		},
		msg: 'Не меньше пяти букв' 
	},
	
	empty: {
		func: function(s){
		s = trim(s)
		return s.length
		},
		msg: 'Обязательное поле' 
	},
	
	addr: {
		func: function(s){
		s = trim(s)
		return s.length > 5 && s.match(/\d/)
		},
		msg: 'Не меньше пяти букв и цифры' 
	},
	
	tel_mob: {
		func: function(s){
		s = s.replace(/\D+/, '')
		return s.match(/\d{6}/)
		},
		msg: 'Не меньше шести цифр' 
	},
	
	tel: {
		func: function(s){
		s = s.replace(/\D+/, '')
		return s.match(/\d{5}/)
		},
		msg: 'Не меньше пяти цифр' 
	}
},

Mult = 
{
	init: function init(id) {
		Mult.box = gi(id);
		Mult.cN = 1;
		
		if (Mult.box) Mult.box.timer2 = setTimeout(Mult.show, 1100)
	},
	
	show: function show() {
		
		Mult.box.timer1 = setInterval(function(){
			Mult.cN += 1;
			Mult.box.className = 'mult kadr' + Mult.cN
			if (Mult.cN > 100) {
				clearInterval(Mult.box.timer1)
			}
		}, 20)
	}
},

Multik = 
{
	init: function init(id) {
		Multik.box = gi(id);
		Multik.cN = 1;
		
		if (Multik.box) {
			Multik.list = gt('div', Multik.box);
			Multik.i = Multik.list.length - 1;
			Multik.list[Multik.i].className = 'img1'
			Multik.box.timer2 = setTimeout(Multik.show, 1900)
		}
	},
	
	nextStep: function nextStep() {
		clearInterval(Multik.box.timer1)
		Multik.box.className = 'kadr1'
		Multik.list[Multik.i].className = 'disnone'
		Multik.i --;
		if (Multik.i > 0) {
			Multik.list[Multik.i].className = 'img1'
			Multik.cN = 1;
			Multik.box.timer2 = setTimeout(Multik.show, 1900)
		}
	},
	
	show: function show() {
		Multik.box.timer1 = setInterval(ranger, 20)
		function ranger(){
			Multik.cN += 2;
			Multik.box.className = 'kadr' + Multik.cN
			if (Multik.cN > 100) {
				Multik.nextStep();
			}
		}
		
	}
},

Slide = 
{
	init: function init(id) {
		Slide.box = gi(id);
		Slide.cN = 1;
		//alert(id + '/' + Slide.box.className)
		if (Slide.box) Slide.show()
	},
	max_height:75,
	show: function show() {
		cc(Slide.box, 'kadr0', 'disnone')
		//alert(Slide.box.className)
		//return;
		Slide.box.timer1 = setInterval(function(){
			Slide.cN += 1;
			Slide.box.className = 'kadr' + Slide.cN
			if (Slide.box.offsetHeidht >= Slide.max_height || Slide.cN > 15) {
				clearInterval(Slide.box.timer1)
				//alert(Slide.box.className)
			}
		}, 10)
	}
},

Msg_user = {
	init: function init(id, subj) {
		var box = gi(id), params = {type:'checkbox', title:'Только мои объекты'},
			knopka = buildField ('input', params), 
			name = 'filter_user_path', value;
		
		if (!box) return;
		box.insertBefore(knopka, box.firstChild);
		knopka.onclick = setUserFilter
		value = getCookie(name)
		knopka.checked = (value) ? true : false;
	}
},

Form = {
	init: function init(id, subj) {
		var frm = gi(id);
		frm.js_subj = subj;
		if (gi('otvet_' + subj)) location.href = '#scroll_' + subj;
		if (window.initForm) initForm(frm)
		var func = 'siteForm_' + subj
		if (window[func]) window[func](frm)
		
	}
},

Backup = {
	init: function init(id, subj) {
		var box = gi(id);
		box.js_subj = subj;
		var table = dict.subjects[subj]['table'], frm = {};
		frm.bd_type = 'Mysql'
		frm.p_key = {table:table}
		setBackupForm(frm, 'таблицу', box)
	}
},

Sortable = {
	t: null, 
	curr_checkbox: null,
	init: function init(id, subj) {
		Sortable.t = gi(id);
		Sortable.t.js_subj = subj;
		/** Следующая операция не прокатывает, так как таблица 
		* перерисовывается. Переносим эту строку в tabsort1.js:Tpage()
		*/
		//if (Sortable[subj]) Sortable[subj]();
		prepTabs(null, Sortable.t)
	},
	schema: function schema() {
		setE(Sortable.t, 'input', {}, {onclick:Sortable.setBoolValue})
	},
	setBoolValue: function setBoolValue(e, o) {
		o = o || this
		
		var value = (o.checked) ? 1 : '',
			src = 'write_bool.com?subj=' + Sortable.t.js_subj 
				+ '&name=' + o.name + '&value=' + value;
		curr_checkbox = o;
		dict.ajax_accepter = Sortable.response; 
		
		scriptRequest(src) 
	},
	response: function response() {
		if (curr_checkbox.checked) cc(curr_checkbox.parentNode, 'checked')
		else cc(curr_checkbox.parentNode, null, 'checked')
	}
},

Catalog = {
	init: function init(id) {
		var c = gi(id);
		if (c) {
			setE(c, 'img', {}, {onclick:showBig})
			document.onkeyup = cancel
		}
	}
},

Profiler = {
	init: function init(id) {
		var c = gi(id);
		if (c) {
			if (dict[id]) c.innerHTML = dict[id];
		}
	}
},

Menu = {
	init: function init(id) {
		var c = gi(id);
		if (c && window[id + 'Init']) {
			window[id + 'Init'](c)
		}
	}
},

MenuTop = {
	init: function init(id) {
		var c = gi(id);
		if (c) {
			
		}
	}
},

MenuLeft = {
	init: function init(id) {
		var box = gi(id), tag = 'span', attrs = {onclick:MenuLeft.expand};
		setE (box, tag, {}, attrs, 'checkActive', 'MenuLeft')
	},

	expandParent: function expandParent(el) {
		var cN = 'disnoneif', li = findParent(el, '', cN);
		while (li) {
			cc(li, null, cN)
			li = findParent(li, '', cN)
		}
	},

	checkActive: function checkActive(el) {
		if (!hc(el.className, 'active')) return
		else MenuLeft.expandParent(el)
	},

	expand: function expand(e, o) {
		o = o || this
		var cN = 'disnoneif', o = o.parentNode;
		if (!hc(o.className, cN)) cc(o, cN)
		else cc(o, null, cN)
	}
},

Voting =
{
	setValues: function setValues(el) {
		var name = el.value, pare = el.parentNode, span = findChild(pare, 'span');
		
		var value = dict.poll_arr[name] || 0
		
		if (span) pare.removeChild(span)
		span = buildEl2('span', {}, ' (' + value + ')')
		ac(span, pare)
		
	},
	
	setPoll: function setPoll(iduser) {
		if (iduser) setCookie2 (Voting.cookie_iduser, iduser, {days:30})
		setE(Voting.box, 'input', {}, {}, 'setValues', 'Voting')
	},

	toVote: function toVote (e, o) {
		o = o || this;
		var name = o.name, value = o.value, iduser = getCookie(Voting.cookie_iduser) || 0;
		
		name = name.replace(/\D+/g, '')
		
		scriptRequest('to_vote.com?topic=' + Number(name) 
			+ ' &opt=' + Number(value) 
			+ ' &iduser=' + iduser)
	},

	init: function init(id) {
		Voting.box = gi(id);
		if (Voting.box) {
			Voting.cookie_iduser = location.hostname + '_iduser';
			setE(Voting.box, 'input', {}, {onclick:Voting.toVote})
		}
	}
},

Comments = 
{
	tpls: {
		p: function(name, el, need){
			var p = ce('p'), span = ce('span'), ast = buildEl2 ('acronym', {className:'need', title:'Заполнять обязательно!'}, '* '), text = ct(name + ': '); 
			ac(text, span); 
			if (need) ac(ast, span); 
			ac(span, p); 
			ac(el, p); return p;
		},
		span: function(name, el, need){
			var span = buildEl2('span', {className:el.name}, el.title), ast = buildEl2 ('acronym', {className:'need', title:'Заполнять обязательно!'}, '* '), text = ct(name + ': '); 
			if (need) ac(ast, span); 
			span.insertBefore(el, span.firstChild)
			return span;
		}
	},
	
	init: function init(id) {
		dict.ajax_accepter = Comments.acceptA
		
		Comments.formClass = Comments.formClass || ''; 
		var head = gi(id), table = head && head.className;
		if (!table) return;
		siteComments(table); //local fields //Допущение: table == subj
		
		var formbox = head.parentNode.insertBefore(ce('div'), head.nextSibling),
			params = {action:'write.com?ajax', 
			method:'post', target:'formframe', 
			onsubmit:Comments.validate, className:'add_message'};
		
		Comments.frm = buildEl2 ('form', params)
		
		if (Comments.formClass == 'disnone') {
			cc(head, 'underlink')
			head.onclick = function(){cc(formbox, null, 'disnone')}
		}
		formbox.className = 'formbox ' + Comments.formClass
		formbox.innerHTML = '<iframe class="formframe disnone" name="formframe" src="about:blank"></iframe>'
		
		Comments.frame = formbox.firstChild
		ac(Comments.frm, formbox)
		Comments.frm.el_fields = {}
		
		for (id in Comments.fields) {
			Comments.addField(id)
		}
		
		Comments.prepFields();
	},
	
	addField: function addField(id) {
		var fields = Comments.fields, o = fields[id], box = Comments.frm,
		prop = o.prop, el, tpl = o.tpl, el_add, need = o.need || false,
		source = o.source || {};
		
		fields[id].prop.name = (o.ru) ? ('set[' + id + ']') : id
		fields[id].prop.title = o.ru
		fields[id].prop.className = id + ' ' + (prop.className || '')
		prop = o.prop
		
		el = buildField(o.el, prop, source)
		el_add = (tpl && prop.type != 'hidden') ? Comments.tpls[tpl](o.ru, el, need) : el
		ac(el_add, box)
		el.cname = id
		Comments.frm.el_fields[id] = el
	},
	
	prepFields: function prepFields() {
		var frm = Comments.frm, fields = Comments.fields, el, val, 
			list = frm.elements, str = '', field, prop;
		for (var i = 0, l = list.length; i < l; i ++) {
			el = list[i]
			field = fields[el.name]
			prop = field && field.prop
			if (prop) {
				for (var p in prop) {
					if (p != 'type' && p != 'className' && p != 'onclick' && p != 'onblur') {
						//str += (el.name + ' / ' + p + ' / ' + prop[p] + '\n')
						el.setAttribute(p, prop[p])
					}
				}
				
			}
			//el.onblur = setCurrItem
		}
		//alert(str)
	},
	
	setCurrItem: function setCurrItem(e, o) {
		e = e || window.event
		var obj = (e) ? (e.target || e.srcElement) : null;
		o = o || this
		setCookie(o.cname, o.value, location.pathname)
	},
	
	validate: function validate(e, f) {
		f = f || this
		var fields = Comments.fields, el, name, o, ru, v, ok, list = f.elements, err = [];
		
		for (var i = 0, l = list.length; i < l; i ++) {
			ok = true
			el = list[i]
			if (el.type == 'file' && !el.value) el.disabled = true;
			v = el.value
			name = el.cname
			o = fields[name]
			if (!o) {err.push(name); continue}
			ru = o.ru || name
			//err.push(ru)
			need = o.need
			if (!need) continue
			func = Validator[need] && Validator[need].func
			if (func) ok = func(v)
			else if (Number(need)) ok = (v.length >= need)
			if (!ok) err.push(ru)
		}
		
		if (err.length) {
			prevent(e)
			alert('Неверно заполнено:\n====================\n' + err.join('\n'))
			return false
		}
		else {
			cc(Comments.frame, null, 'disnone')
			return true
		}
	},
	
	hide_frame: function hide_frame () {
		cc(Comments.frame, 'disnone')
	},
		
	acceptA: function acceptA() {
		var list = gt('div'), el, box, msg = ce('div'),
		data = dict.ajax_data && dict.ajax_data[1];
		
		for (var i = 0, l = list.length; i < l; i ++) {
			el = list[i]
			cN = el.className
			if (hc(cN, 'messages')) {
				box = el;
				break;
			}
		}
		if (!data || !box) return;
		
		name = data.name
		text = data.text
		time = data.time || ''
		msg.className = 'item'
		msg.innerHTML = '<span class="name">' 
		+ name + '</span><span class="mtime">' 
		+ time + '</span><div>' 
		+ text + '</div>'
		ac(msg, box)
		Comments.hide_frame()
	}

},

Files = 
{
	init: function init(id) {
		Files.formClass = Files.formClass || ''; 
		var head = gi(id), table = head && head.className;
		if (!table) return;
		siteFiles(table); 
		
		var formbox = head.parentNode.insertBefore(ce('div'), head.nextSibling),
			params = {
				action:'write.com', 
				method:'post', 
				enctype: 'multipart/form-data',
				onsubmit:Comments.validate, 
				className:'add_file'
			};
		
		Files.frm = buildEl2 ('form', params)
		if (Files.formClass == 'disnone') {
			cc(head, 'underlink')
			head.onclick = function(){cc(formbox, null, 'disnone')}
		}
		formbox.className = 'formbox ' + Files.formClass
		ac(Files.frm, formbox)
		Files.frm.el_fields = {}
		
		for (id in Files.fields) {
			Files.addField(id)
		}
		
		Files.prepFields();
	},
	
	addField: function addField(id) {
		var fields = Files.fields, o = fields[id], box = Files.frm,
		prop = o.prop, el, tpl = o.tpl, el_add, need = o.need || false,
		source = o.source || {};
		
		fields[id].prop.name = (o.ru && o.prop.type != 'file') ? ('set[' + id + ']') : id
		fields[id].prop.title = o.ru
		fields[id].prop.className = id + ' ' + (prop.className || '')
		prop = o.prop
		
		el = buildField(o.el, prop, source)
		el_add = (tpl && prop.type != 'hidden') ? Comments.tpls[tpl](o.ru, el, need) : el
		ac(el_add, box)
		el.cname = id
		Files.frm.el_fields[id] = el
	},
	
	prepFields: function prepFields() {
		var frm = Files.frm, fields = Files.fields, el, val, 
			list = frm.elements, str = '', field, prop;
		for (var i = 0, l = list.length; i < l; i ++) {
			el = list[i]
			field = fields[el.name]
			prop = field && field.prop
			if (prop) {
				for (var p in prop) {
					if (p != 'type' && p != 'className' && p != 'onclick' && p != 'onblur') {
						//str += (el.name + ' / ' + p + ' / ' + prop[p] + '\n')
						el.setAttribute(p, prop[p])
					}
				}
				
			}
			//el.onblur = setCurrItem
		}
		//alert(str)
	}
	
},

Karta = {
	init: function init(id) {
		/** @require Яндекс-скрипт, что-то вроде:
		* http://api-maps.yandex.ru/1.1/index.xml?key=AFfroU8BAAAAvXVFawIAzSFbR6Wb4cwRZivvJA956Kyq0xcAAAAAAAAAAAANzEpiJ2cRYLSqnud2v0fQ0si9zg==
		* @require HTML div element id='karta'
		* @require dict.config.map
		*/
		var box = gi(id);
		if (!window.YMaps || !box) return alert(('YMaps: ' + window.YMaps) + ('\nbox: ' + box))
		var div = ac(ce('div'), box), options = dict.config.map;
		div.className = 'ymap';
		map = new YMaps.Map(div);
		//alert(options.h + options.v + options.level + options.title)
		map.setCenter(new YMaps.GeoPoint(options.h, options.v), options.level);
				
			/*
			var s = new YMaps.Style();
			s.iconStyle = new YMaps.IconStyle();
			s.iconStyle.href = "img/vhod2.jpg";
			s.iconStyle.size = new YMaps.Point(200, 168);
			s.iconStyle.offset = new YMaps.Point(-200, -168); //, {style: s}
			*/
				
		var placemark = new YMaps.Placemark(map.getCenter());
		placemark.setBalloonContent('<img src="' 
			+ dict.config.uri_prefix + '/files/' + options.balloon_img + '" alt="' 
			+ options.balloon_img + '">');
		placemark.setIconContent(options.title);
		map.addOverlay(placemark);
	   
		map.addControl(new YMaps.TypeControl());
		map.addControl(new YMaps.ToolBar());
		map.addControl(new YMaps.Zoom());
		//map.addControl(new YMaps.MiniMap());
		map.addControl(new YMaps.ScaleLine());
		//map.addControl(new YMaps.SearchControl());
	}
}
;

-function markers () 
{
	capitalize = function capitalize (s) 
	{
		if (s) {
			var a = s.charAt(0);
			s = s.replace(new RegExp('^' + a), a.toUpperCase())
		}
		return s;
	}
	
	var initMarkers = function initMarkers () 
	{
		if (!window.dict) window.dict = {}
		cc(document.body, null, 'noJS')
		cc(document.body, 'withJS')
		var arr = dict.markers, marker, worker;
		if (!arr) return;
		for (var m in arr) {
			worker = capitalize(m)
			if (!window[worker]) continue;
			for (var id in arr[m]) {
				marker = m + arr[m][id]['num_id']
				window[worker].init(marker, arr[m][id]['subj'])
			}
		}
	}
	
	addLoadEvent(initMarkers)
	
}()

function scriptRequest(src) {
 var el=document.createElement("SCRIPT");
 el.type="text/javascript";
 document.body.appendChild(el);
 el.src=src;
}

function prevent(e) {
 e = e || window.event
 e.cancelBubble = true
 if (e.stopPropagation) {
  e.stopPropagation()
  e.preventDefault()
 }
}

function hidTemp() { /* Спрятать последнее (!) всплывшее <>*/
	tempobj=window.tempobj || window.parent.tempobj //вызов из iframe
	if (!tempobj || !tempobj.length) return
	var el = tempobj.pop()
	if (el && el.tagName) {
		if (dict && dict.curr_big_img == el) showBig(null, el)
		else cc(el, hiddclass)
	}
	if (window.d && d.active_input) {
		d.active_input.cpl = true
		cc(d.active_input, null, hiddclass)
	}
}

function initvar(c) {
 if (!window.d || "object" != typeof window.d) window.d={}
 c = c || window.c
 if (!c) return
 for (var prop in c) {
  var b=c[prop]
  if (0<b.length)
   d[b]=document.getElementById(b)
 }
}

function addLoadEvent(func) {
	var old = window.onload
	if (typeof old != 'function') window.onload = func
	else window.onload = function() { old(); func(); }
}

function findParent(el, tag, cN){ 
	
	if (!el || !el.tagName) return null
	tag = tag || '.*'; cN = cN || '.*'
	var body = document.body
	
	if (tag && tag.tagName) { /* м.б. объект элемент */
		while (el && body != el.parentNode) {
			el = el.parentNode
			if (el == tag) return el
		}
		return null
	}
	
	tag = new RegExp('^(' + tag + ')$', 'i')
	cN = new RegExp('(^|\\s)(' + cN + ')(\\s|$)', 'i')
	
	while (el.parentNode) {
		if (body == el) break;
		el = el.parentNode //не сам объект!!
		if (el.tagName && el.tagName.match(tag) && el.className.match(cN)) return el
	}
	return null
}

function findChild(obj, tag, classn, rec) {
	if (!obj) return null
	if (!obj.hasChildNodes()) return null
	var oTag 
	var el, el_class, list = (rec) ? obj.getElementsByTagName(tag) : obj.childNodes
	var l = list.length
	for (var i=0; i<l; i++) {
		el = list[i]
		oTag = el.tagName
		el_class = el.className
		if (oTag && tag.toLowerCase()==oTag.toLowerCase() 
			&& (!classn || hc(el_class, classn))) {
			return el
			break
		}
	}
	return null
}

function hc(cN /*string only*/, c) { /*hasClass*/
	return (!c || !cN) ? false : ((" " + cN + " ").indexOf(" " + c + " ") !== -1)
}

function cc(o, add, del) { /*cnangeClass*/
	var n = "className", cN = (undefined != o[n]) ? o[n] : o, ok = 0
	if ("string" !== typeof cN) return false
	var re = new RegExp('(\\s+|^)' + del + '(\\s+|$)', 'g')
	if (add) /*addClass*/
		if (!hc(cN, add)) {cN += " " + add; ok++}
	if (del) /*delClass*/
		if (hc(cN, del)) {cN = cN.replace(re, " "); ok++}
	if (!ok) return false
	if ("object" == typeof o) o[n] = cN 
	else return cN
}

function fixEvent(e) {
	// получить объект событие для IE
	e = e || window.event

	// добавить pageX/pageY для IE
	if ( e.pageX == null && e.clientX != null ) {
		var html = document.documentElement
		var body = document.body
		e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0)
		e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0)
	}

	// добавить which для IE
	if (!e.which && e.button) {
		e.which = e.button & 1 ? 1 : ( e.button & 2 ? 3 : ( e.button & 4 ? 2 : 0 ) )
	}

	return e
}

function getTopLeft(el) {
	var top=0, left=0
	while(el) {
		top = top + parseInt(el.offsetTop)
		left = left + parseInt(el.offsetLeft)
		el = el.offsetParent
	}
	return {top:top, left:left}
}


function gi(i, d) {d = d || document; return d.getElementById(i)}
function ge(i, d) {d = d || document; return d.getElementById(i)}
function ce(t, d) {d = d || document; return d.createElement(t)}
function ct(tx, d) {d = d || document; return d.createTextNode(tx)}
function gt(t, e) {e = e || document; return e.getElementsByTagName(t)}
function ac (n, e) {e = e || document.body; 
	try {return e.appendChild(n);}
	catch(e){alert(n.innerHTML)}
	
}


function fixTime() {
	var d1, d0 = new Date(), ret, i, 
		obj = arguments[0], f = arguments[1],
		args = Array.prototype.slice.call(arguments, 2)
	obj = obj || window
	if (!(f in obj) || typeof obj[f] !== 'function') return
	ret = obj[f].apply(this, args)
	d1 = new Date()
	Log(d1 - d0, f)
	return ret
}

function getCookie(name) {
	if (name && new RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)').test(document.cookie)) {
		return unescape(RegExp.$1);
	}
}

function deleteCookie (name, attrs) 
{
	attrs = attrs || (window.dict && window.dict.c_attrs) || {};
	attrs.days = -1;
	setCookie2 (name, '', attrs)
}

function checkCookie() {
	setCookie('a', 1);
	return getCookie('a');
}

function setCookie2 (name, value, attrs) 
{
	if (!name) return
	attrs = attrs || (window.dict && window.dict.c_attrs) || {};
	
	var d = null, days = attrs.days, ms = attrs.expires;
	
	if (days) {
		d = new Date();
		d.setDate(d.getDate() + days);
		delete(attrs.days)
	}
	
	else if (ms) {
		d = new Date();
		if (isNaN(ms)) ms = Date.parse(ms);
		d.setTime(ms)
	}
	
	if (d) attrs.expires = d.toGMTString();
	
	var cookie = escape(name) + '=' + escape(value || '');
	for (var id in attrs) {cookie += '; ' + id + '=' + attrs[id]}
	
	document.cookie = cookie;
}

function setCookie(name, value, path, time) {
 path = (path) ? "; path=" + path : ""
 var expires = new Date()
 if (isNaN(time)) expires = ""
 else {
  if (time < 0) time = -(12*60*60*1000)
  else if (time === 0) time = 36*24*60*60*1000
  expires.setTime(expires.getTime()+time)
  expires = expires.toGMTString()
  expires = "; expires=" + expires
 }
 name=("string"==typeof(name) && name) || this.name || this.id
 value = (undefined == value) ? this.value : value
 value=(this.type && "checkbox"==this.type && "on"==value)?1:value
 value=(this.type && "select-one"==this.type)?this.selectedIndex:value
 
 document.cookie = escape(name) + "=" + escape(value) + expires + path
}

function cancel(e) {
 e = e || window.event
 if (27==e.keyCode) hidTemp()
}

function hex2dec(str){
 return (parseInt(str,16))
}

function dec2hex(num){
 return num.toString(16)
}

function dec2bin(num){
 return num.toString(2)
}

function trim(s) {
	if (!s) return '';
	if (!s.replace) return s;
	s = s.replace(/\&nbsp\;/g, "\xa0");
	return s.replace(/^\s+|\s+$/, "");
}

function stripTags(s) {
	if (!s.replace) {return s;}
	return s.replace(/\<[^\<\>]+\>/ig,"");
}

function showChildSpan(obj, timerId) {
 var that = (this.tagName) ? this : obj
 if (that.timer) clearTimeout(that.timer)
 cc(that, null, "hide_ul")
}

function hideChildSpan(obj, timerId) {
 var that = (this.tagName) ? this : obj
 that.timer = setTimeout(function(){cc(that, "hide_ul"); }, 900)
}

function setchbox(el) {
  if (!(el && el.type && "checkbox"==el.type)) return
  gc=parseInt(getCookie(el.id))
  if (gc) { /* else значения из PHP */
   ch=(1==gc)?true:false
   el.checked=ch
  }
}

function hidd(obj, yes) {
 if (yes) cc(obj, "disnone")
 else cc(obj, null, "disnone")
}

function getStyleVal(el, name) {
 var style = (window.getComputedStyle) ? window.getComputedStyle(el, null) : el.currentStyle
 return style && style[name]
}

function sqw_1() {
	return dec2hex(parseInt(d.artbox[0]) + parseInt(d.artbox[1]))
}

function tre_1(d1, d2) {
	return dec2hex(parseInt(d.artbox[0]) * parseInt(d.artbox[1]))
}

function amt_1(d1, d2) {
	return dec2bin(parseInt(d.artbox[0]) + parseInt(d.artbox[1]))
}

function pravda(){
	return confirm('Вы уверены?')
}

function build_x(doc) {
	/** Это "модальный" крест. Он может быть одновременно только у одного объекта.
	*  И это надо менять.
	 **/
	 doc = doc || document
	if (!window.d || "object" != typeof window.d) window.d = {}

	if (!window.dict || "object" != typeof window.dict) window.dict = {}
	if (d.krest) {return}
	d.krest = dict.krest = ac(buildEl2('button', {className: 'krest disnone'}, 'x', doc), doc.body);
	return dict.krest;
}

function addKrest(box, pos) {
	var krest = dict.krest || build_x(), coo = getTopLeft(box), w = box.offsetWidth;
	krest = krest.cloneNode(true);
	krest.onclick = function(){cc(box, 'disnone'); }
	if (pos == 2) {
		cc(krest, 'krest2', 'krest')
		
	}
	cc(krest, null, 'disnone')
	ac(krest, box)
	
	//krest.style.top = coo.top - 10 + 'px'
	//krest.style.left = coo.left + w - 20 + 'px'
	krest.style.zIndex = 9
	if (window.tempobj) tempobj.push(box)
}

function buildField (el, params, idxs) {
	if (params.type == 'radio') return buildRadio (el, params, idxs)
	var el = ce(el), name = params.name, value = params.value;
	for (var id in params) {
		//try{el.setAttribute(id, params[id])}
		//catch(e){}
		try{el[id] = params[id]}
		catch(e){}
		
	}
	if (idxs) {
		
		var params;
		for (id in idxs) {
			params = {value:id}
			if (id == value) params.selected = true
			ac(buildEl2('option', params, idxs[id]), el)
		}
	}
	return el
}

function buildRadio (el, params, idxs) {
	var field, span, p = ce('p'), name = params.name, value = params.value;
	for (var id in params) el[id] = params[id]
	if (idxs) {
		var params;
		for (id in idxs) {
			field = ce(el)
			field.type = 'radio'
			field.name = name
			field.value = id
			span = buildEl2('span', {}, idxs[id])
			span.insertBefore(field, span.firstChild)
			ac(span, p)
		}
	}
	p.insertBefore(buildEl2('span', {className:'radio_label'}, params.title + ':'), p.firstChild)
	return p
}
/*
<input type="radio" name="labeled" value="1" id="labeled_1" /><label for="labeled_1">Раз</label>
*/
function buildEl2 (el, params, txt, d) {
	d = d || document
	txt = txt || undefined
	var el = ce(el, d);
	for (var id in params) {
		el[id] = params[id]
	}
	if (txt != undefined) el.appendChild(ct(txt, d))
	return el
}

function htm2txt (h) {var div = ce('div'); div.innerHTML = h; return div.firstChild.nodeValue}

var Entity = {
	/* by Rumata, http://code.google.com/p/jsxt/source/browse/trunk/js/web/Entity.js*/
	encode: function(s){
	        var div = ce('div'), text = ct(s);
	        ac(text, div);
	        return div.innerHTML.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
	}, 

	decode: function(s){
	        var textarea = ce('textarea');
	        textarea.innerHTML = s;
		
	        return textarea.value;
	}

};

function fetch(obj) {
 var msg = '', val, own
 for (var prop in obj) {
  if (prop) {
   try {val = obj[prop]}
   catch (e) {val = e && e.message + "!!"}
  }
  try {own = obj.hasOwnProperty(prop)}
  catch (e) {own = false}
  prop = own ? "<em>" + prop + "</em>" : prop || prop.join()
  msg += "<strong>" + prop + "</strong>" + ": " + val + "<br>\n"
 }
 return msg
}

function findObj(arr, key, div) {
	div = div || ':'
	key = key.split(div)
	var res = {}, mode = NaN, func, j, id;
	mode = key.pop();
	for (var i = 0, l = key.length; i < l; i ++) {
		arr = arr && arr[key[i]]
	}
	
	if (!isNaN(mode)) {
		j = mode - 1
		for (id in arr) res[++ j] = arr[id]
		//alert(res[j] + '/' + j + '/' + arr[id] + '/' + id)
	}
	else {
		res = arr && arr[mode]
		func = 'build_' + mode;
		if (!res && func && window[func]) res = window[func]()
	}
	//alert(func)
	return res
}

function computeHours (list) 
{
	/** Для таблицы mod_order_log, вычисляет интервал между записями с разными метками и одинаковым id.
	 * Должны быть поля: metka, mtime, id
	 * Записи должны быть насортированы по mtime DESC.
	 * Ищет в записях первую (последнюю по времени!) метку 3 и считает её концом работы.
	 * Ищет следующие записи, и если у них метка не 3, добавляет полученный интервал к общей сумме.
	 * @require parseMDate(), toH()
	 */
	
	var row, metka, idcrit, mtime, init = false, _log = '',
		summa = 0, tmp, prev = 0, more_time, k_hour = 1000 * 60 * 60, 
		begin = 9 /** Начало дня */, end = 18 /** Конец дня */, usef_h = end - begin,
		days, mtime_arr, delta, box = gi('computed');
	
	for (var i = 0, l = list.length; i < l; i ++) {
		row = list[i]
		metka = row.metka
		if (idcrit != row.id && init) break;
		idcrit = row.id
		mtime = parseMDate(row.mtime, begin, end)
		
		if (metka != 3 && init) {
			days = more_time[0].getDate() - mtime[0].getDate()
			if (!days) { //в пределах дня
				delta = more_time[0] - mtime[0]
			}
			else {
				tmp1 = mtime[2] - mtime[0] //полезные часы в последний день интервала
				if (tmp1 < 0) tmp1 = 0
				
				tmp2 = more_time[0] - more_time[1] //часы в первый день
				if (tmp2 < 0) tmp2 = 0
				
				delta = tmp1 + tmp2
				if (days > 1) delta += (days - 1) * usef_h * k_hour
			}
			_log += '<br>Интервал: ' + mtime[0].toLocaleString() + ' -' + more_time[0].toLocaleString() + ' (часы: ' + toH(delta) + ');'
			summa += delta
		}
		if (metka == 3) {
			if (!init) _log += '<br>Конец работы: ' + mtime[0].toLocaleString() + '; '
			init = true
		}
		prev = metka
		more_time = mtime
	}
	summa = toH(summa)
	_log = '<br>Начало работы: ' + mtime[0].toLocaleString() + '; ' + _log
	if (box) box.innerHTML = 'Время работы в часах (id=' + idcrit + '): ' + summa + _log
	
	function toH(ms) {
		ms = ms / k_hour;
		var ms0 = Math.floor(ms)
		return ms0 + ':' + Math.round((ms - ms0) * 60)
	}
	
	function parseMDate(mtime, begin, end) {
		/** @param mtime: '2012-01-01 12:22:13' 
		 * @return array(Время_из_mysql, Время_начала_рабочего_дня, Время_конца_дня)
		*/
		var time = trim(mtime).split(/\D+/);
		if (time.length < 6) {
			for (var i = time.length, l = 6; i < l; i ++) time[i] = 0;
		}
		time[1] -= 1
		time_begin = new Date(time[0], time[1], time[2], begin, 0, 0)
		time_end = new Date(time[0], time[1], time[2], end, 0, 0)
		time = new Date(time[0], time[1], time[2], time[3], time[4], time[5])
		return [time, time_begin, time_end]
	}

}

function checkThumb(frm, e1, e2, force){
	force = force || false
	e1 = e1 || 'onmouseover'
	e2 = e2 || 'onmouseout'
	var list = gt('img', frm), el, cN, src;
	for (var i = 0, l = list.length; i < l; i ++) {
		el = list[i]
		if (el.src && (hc(el.className, 'thumbnail') || force)) {
			el[e1] = showBig
			el[e2] = showBig
		}
	}	
}

function showBig(e, o){
	o = o || this
	var w = o.offsetWidth, h = o.offsetHeight, limg = ce('img'),
		loading = dict.config.uri_prefix + '/img/lightbox-ico-loading.gif';
	limg.src = loading
	limg.className = 'loading'
	if (o.old) {
		//cc(o, null, 'abs big_img')
		o.style.width = ''; 
		o.style.height = ''; 
		o.parentNode.style.height = o.old_height + 'px';
		o.style.top = ''
		o.style.left = ''
		o.style.marginTop = ''
		o.style.marginLeft = ''
		o.className = 'thumb'
		o.src = o.old
		o.old = null
		o.title = ''
		if (o.limg && o.limg.parentNode == o.parentNode) 
			o.parentNode.removeChild(o.limg)
		dict.curr_big_img = false
		
	}
	else if (!dict.curr_big_img) {
		o.old = o.src
		var pare = o.parentNode;
		o.old_height = pare.clientHeight || pare.offsetHeight;
		pare.style.width = w + 'px'
		pare.style.height = h + 'px'
		o.style.width = w * 2 + 'px'
		o.style.height = h * 2 + 'px'
		o.style.backgroundColor = '#999'
		//alert(limg)
		o.limg = pare.appendChild(limg)
		o.style.top = ''
		o.style.left = ''
		o.style.marginTop = ''
		o.style.marginLeft = ''
		o.className = 'pre_big_img'
		o.top = o.offsetTop
		o.left = o.offsetLeft
		o.src = dict.config.uri_prefix + '/files/' + o.alt
		o.onload = function () {
			o.style.width = ''; 
			o.style.height = '';
			var top = o.top - o.offsetHeight / 2 + 100;
			var left = o.left - o.offsetWidth / 2 + 150;
			if (top < 5) top = 5
			if (left < 5) left = 5
			o.style.top = top + 'px'
			o.style.left = left + 'px'
			o.style.marginTop = 0
			o.style.marginLeft = 0
			//alert(o.offsetTop + '/' + o.offsetHeight)
			if (o.limg && o.limg.parentNode == pare) pare.removeChild(o.limg)
		}
		
		o.title = 'Щёлкните, чтобы уменьшить'
		dict.curr_big_img = o
		if (!window.tempobj) tempobj = []
		tempobj.push(o)

	}
}

function setE(box, tag, conditions, attrs, func, obj) {
	if (!box) return;
	conditions = conditions || {}
	attrs = attrs || {}
	
	var list = gt(tag, box), el, id, ok;
	
	if (func) {
		obj = (obj) ? window[obj] : window;
		if (obj && obj[func]) func = obj[func]
		else func = null
	}
	
	for (var i = 0, l = list.length; i < l; i ++) {
		el = list[i]
		ok = true
		
		for (id in conditions) if (!hc(el[id], conditions[id])) ok = false
		if (ok) {
			for (id in attrs) {
				el[id] = attrs[id]
			}
			if (func) func(el)
		}
	}	
}

function translit(e, str) 
{
	var s, rus = /[^a-z0-9_-]+/ig, o = {'ай':'ay', 'ой':'oy', 'уй':'uy', 'ый':'iy', 
		'ей':'ey', 'ий':'iy', 'юй':'yuy', 'ёй':'yoy', 'эй':'ey', 'яй':'ay', 
		'ье':'ie', 'ьо':'io', 'ъе':'ie', 
		'а':'a',  'б':'b',  'в':'v',  
		'г':'g',  'д':'d',  'е':'e',   'ё':'yo',  'ж':'zh',  'з':'z',  
		'и':'i',  'й':'y',  'к':'k',  'л':'l',   'м':'m',  'н':'n',  'о':'o',  
		'п':'p',  'р':'r',  'с':'s',  'т':'t',  'у':'u',  'ф':'f',  'х':'kh',  
		'ц':'ts',  'ч':'ch',  'ш':'sh',  'щ':'shch',  'ы':'y',  'э':'e',  
		'ю':'yu',  'я':'ya',  'А':'A',  'Б':'B',  'В':'V',  'Г':'G',  
		'Д':'D',  'Е':'E',  'Ё':'Yo',  'Ж':'Zh',  'З':'Z',  'И':'I',  'Й':'Y',  
		'К':'K',  'Л':'L',  'М':'M',  'Н':'N',  'О':'O',  'П':'P',  'Р':'R',  
		'С':'S',  'Т':'T',  'У':'U',  'Ф':'F',  'Х':'Kh',  'Ц':'Ts',  'Ч':'Ch',  
		'Ш':'Sh',  'Щ':'Shch',  'Ы':'Y',  'Э':'E',  'Ю':'Yu',  'Я':'Ya',  
		'ь':'',  'Ь':'',  'ъ':'',  'Ъ':''};
	
		
	if (rus.test(str)) {
		for (var id in o) {
			if (str) {
				s = str
				str = str.replace(new RegExp(id, 'g'), o[id])
			}
			else {alert(id); break;}
		}
		//alert(s + id)
		str = str.replace(rus, '_');
	}
	return str;
}

function buildFilename(e, str) {
	return translit(e, str) + '.html'
}

function getDescr(e, o) {
	o = o || this
	var frm = o.form, source_field = frm.cn_fields[o.formula],
		value = source_field && source_field.source_row 
			&& source_field.source_row.descr;
	
	if (value) o.value = value
}

function addExtJpg(e, o) {
	o = o || this
	var frm = o.form, value = frm.cn_fields[o.formula].value;
	o.value = value + '.jpg'
}

function urlTr(e, o) {
	o = o || this
	var frm = o.form, value = frm.cn_fields[o.formula].value;
	o.value = translit(e, value)
}

function strTr(e, o) {
	o = o || this
	var frm = o.form, value = frm.cn_fields[o.formula].value;
	o.value = translit(e, value) 
}

function sheduler_tpl_05 (d1, d2, h1, h2) {
	var start = 0, kod, day, time, tmp, dmin = 1, dmax = 7, hmin = 0, 
		hmax = 48, days = dict.config.site.dw1;
	
	if (!d1) d1 = dmin;
	if (!d2) d2 = dmax;
	if (!h1) h1 = hmin;
	if (!h2) h2 = hmax;
	$ret = {}
	for (var i in days) {
		start = (i - 1) * hmax;
		for (var j = hmin; j <= hmax; j ++) {
			kod = start + j;
			day = days[i]
			tmp = j / 2;
			time = Math.floor(tmp)
			time += (time != tmp) ? ':30' : ':00'
			$ret[kod] = day + ', ' + time
		}
	}
	
	return ret;
}

function build_time_list (h1, h2, step) {
	var period = 60, hmin = 8, hmax = 18, res = {}, id;
	if (!h1) h1 = hmin;
	if (!h2) h2 = hmax;
	if (!step) step = 30;
	
	for (var i = hmin; i <= hmax; i ++) {
		for (var j = 0; j < period; j += step) {
			
			id = i + ':' + ((j < 10) ? '0' : '') + j
			res[id] = id
		}
	}
	return res;
}

function showTimeList() {
	
}

function fixTime() {
	var d1, d0 = new Date(), ret, i, 
		obj = arguments[0], f = arguments[1],
		args = Array.prototype.slice.call(arguments, 2)
	obj = obj || window
	if (!(f in obj) || typeof obj[f] !== 'function') return
	ret = obj[f].apply(this, args)
	d1 = new Date()
	Log(d1 - d0, f)
	return ret
}



function buildTHeaders(t, token) {
	var h = t.rows[0], cells = h.cells, cell, cn;
	t.header = [];
	t.header_inv = {}
	for (var i = 0, l = cells.length; i < l; i ++) {
		cell = cells[i]
		cn = cell[token] /** Жёсткий класснэйм (без пробелов!) */
		t.header[i] = cn
		t.header_inv[cn] = i
		
	}

}


/** Calendar */
-function () {
	if (!window.dict) window.dict = {}
	if (!dict.config) dict.config = {}
	var dc = (dict.config.calendar) || {}, d = document, year_offset = dc.year_offset || 0,
	year0 = parseInt(dc.year0) || new Date().getFullYear(),
	red_day = 'red',
	hover = 'hover',
	lang = {
		correctDn: function(d){return (d == 0) ? 6 : d - 1},
		makeDate: function(s){s = s.split(/\D+/); return new Date(s[2], s[1] - 1, s[0])},
		formatDate: function (d) {
			d = [d.getDate(), d.getMonth() + 1, d.getFullYear()]
			return d.join(dict.config.site.date_div || '.').replace(/\b(\d)\b/g, '0$1')
		}
	},
	box = ce('div');
	box.className = 'mm disnone';

	var dict2lang = function dict2lang () {
		var pre = {'dn':'dw2', 'mm':'mm2'}, pre_arr, i, j, id;
		for (id in pre) {
			lang[id] = []
			pre_arr = dict.config[pre[id]]
			i = -1
			for (j in pre_arr) {
				lang[id][++ i] = pre_arr[j]
			}
		}
	}
	dict2lang ()
	
	var buildCArr = function (y) {
		var d0 = new Date(y, 0, 1), c = [], dd, dn, mm;
		for (var i = 0; i < 12; i ++) {c[i] = []}
		while (d0.getFullYear() == y) {
			dd = d0.getDate()
			dn = lang.correctDn(d0.getDay())
			mm = d0.getMonth()
			c[mm][dd] = dn
			d0.setDate(dd + 1)
		}
		
		return c;
	}

	var setDate = function (e) {
		e = e || window.event
		var o = e.target || e.srcElement
		if (!o.tagName || o.tagName.toLowerCase() != 'div') return
		var d = o.firstChild.nodeValue;
		
		this.input.date = new Date(this.year, this.month, d)
		this.input.value = lang.formatDate(this.input.date)
		
		cc(this, 'disnone')
		this.input.onblur = hideMonth
		this.input.onfocus = showMonth
	}
	
	
	var hideMonth = function (e, o) {
		e = e || window.event
		o = o || this
		o.onfocus = showMonth
		if (e && o.calendar && !hc(o.calendar.className, 'disnone')) 
			setTimeout(function(){cc(o.calendar, 'disnone')}, 400) 
	}
	
	var getEvent = function(d){
		d = d.replace(/\b(\d)\b/g, '0$1')
		return dict.calendar.table[d] 
	}
	
	var showMonth = function (e, dd, el) {
		e = e || window.event
		var oInput = e && (e.target || e.srcElement)
		dd = dd || this.date;
		if (!dd) {
			dd = new Date()
			dd.setFullYear(year0);
		}
		var o = el || this;
		o.onfocus = '';
		
		if (o.timer) {clearTimeout(o.timer); o.timer = null}
		if (e && e.type == 'focus') return (o.timer = setTimeout(function(){showMonth(null, dd, o)}, 400))
		
		if (e && e.type == 'click' && oInput == o && o.calendar && !hc(o.calendar.className, 'disnone')) 
			return cc(o.calendar, 'disnone')
		
		var Y = dd.getFullYear(), c = buildCArr(Y), m = dd.getMonth(), 
		 arr = c[m], cN, curr_day = dd.getDate(),
		 marr = [], htm = '<table><col><col><col><col><col><col class="red"><col class="red">', 
		 week = new Array(7), coo = getTopLeft(o), inputs = o.form.inputs, scroll = inputs && inputs.scrollTop || 0,
		 flush = function(arr){marr.push('<td>' + arr.join('</td><td>') + '</td>'); return new Array(7)};
		
		if (!o.calendar) {
			o.calendar = ac(box.cloneNode(true))
		}
		
		var c_div = o.calendar, title;
		
		htm += '<tr><th>' + lang.dn.join('</th><th>') + '</th></tr>'
		
		for (var i = 1, l = arr.length; i < l; i ++) {
			//if (arr[i] == undefined) continue;
			cN = []
			title = getEvent(Y + '-' + (m + 1) + '-' + i) || ''
			if (title) title = ' title="' + title + '"'
			if (arr[i] > 4) cN.push(red_day)
			if (i == curr_day) cN.push(hover)
			if (title) cN.push('metka')
			cN = (cN.length) ? ' class="' + cN.join(' ') + '"' : ''
			week[arr[i]] = '<div' + cN +  title + '>' + i + '</div>'
			if (arr[i] === 6) week = flush(week)
		}
		flush(week)
		
		htm += '<tr>' + marr.join('</tr><tr>') + '</tr></table>';
		c_div.innerHTML = htm
		
		var p = ce('p'), months = buildField('select', {}, lang.mm),
		 years = {}, yl = year_offset * 2 + 1, y0 = Y - year_offset;
		
		for (var i = 0; i < yl; i ++) {years[y0] = y0++}
		years = buildField('select', {}, years)
		ac(years, p)
		years.selectedIndex = year_offset
		years.onchange = function(){showMonth(null, new Date(this.value, m, 1), o)}
		
		ac(months, p)
		cc(p, 'ri')
		
		c_div.insertBefore(p, c_div.firstChild)
		months.selectedIndex = m
		months.onchange = function(){showMonth(null, new Date(Y, this.value, 1), o)}
		
		if (!window.tempobj) tempobj = []
		if (hc(c_div.className, 'disnone')) {
			cc(c_div, null, 'disnone')
			tempobj.push(o.calendar)
			
			var pageY = e && e.clientY || 0, c_height = c_div.offsetHeight + 10, th_height = o.offsetHeight + 10,
			 c_top = (pageY > c_height) ? coo.top - c_height : coo.top + th_height;
			o.calendar.style.left = coo.left + 'px'
			o.calendar.style.top = c_top - scroll + 'px'
		}
		
		c_div.input = o
		c_div.month = m
		c_div.year = Y 
		c_div.onclick = setDate
		c_div.onmouseover = function(){o.onblur = '';}
		c_div.onmouseout = function(){o.onblur = hideMonth;}
		o.focus()
		
	}

	
	window.initCalendar = function initCalendar(e, el) {
		d.onkeyup = cancel
		var inputs = (el) ? [el] : gt("input") , inp;
		
		for (var e in inputs) {
			inp = inputs[e]
			if (!hc(inp && inp.className, "date")) continue;
			inp.onclick = showMonth 
			inp.onfocus = showMonth 
			inp.onblur = hideMonth 
		}
		
	}
	
	addLoadEvent(initCalendar)
	
}()

function isKeySlovo (key) {
	var key_slovo = /^[a-z][a-z0-9_]*$/i;
	return key_slovo.test(key)
}
function isInt(data_type) {
	return /int/i.test(data_type)
}

function attract(field, message, cN) {
	if (!cN) cN = 'err_inner'
	if (!message) message = 'Это ключевое поле. Оно должно начинаться с буквы и состоять из латинских букв и цифр.'
	message = ct(message)
	field.focus()
	if (!field.mess_box) {
		var mess_box = field.parentNode.insertBefore(ce('p'), field.nextSibling)
		cc(mess_box, cN)
		ac(message, mess_box)
		field.mess_box = mess_box
		field.onfocus = function(){if (this.mess_box) this.mess_box.parentNode.removeChild(this.mess_box); field.mess_box = null;}
		//field.onkeyup = 
	}
	return false
}

function removeNodes(box, tag, cN) {
	var list = gt(tag, box), i = list.length, el, pare;
	while (i --) {
		el = list[i]
		pare = el.parentNode
		if (hc(el.className, cN)) pare.removeChild(el)
	}
	
}

function findArrItem (arr, pk_arr) {
	/** @param arr: ассоциативный! pk_arr должен быть тоже! 
	* @return {id:id, row:row}, где id - ключ arr, row - элемент arr с данным ключом
	*/
	var i, row, id, flag;
	dict.ajax_arr_len = 0;
	for (i in arr) {
		dict.ajax_arr_len ++
		row = arr[i]
		
		flag = true
		for (id in pk_arr) {
			//addLog(i + '|' + id + '=' + row[id] + '|')
			
			if (pk_arr[id] != row[id].toLocaleLowerCase()) flag = false
		}
		
		if (flag) {
			
			return {id:i, row:row};
		}
	}
	return null
}

function setCellValue(cell, value, link) {
	/** Ищет единственную самую глубокую и самую последнюю текст Ноду
	 * Рассчитано на ячейку со ссылкой на открытие формы. Очень криво и ненадёжно
	 */
	if (!cell.tagName || cell.tagName.toLowerCase() != 'td') return;
	var text_node, last_el = null;
	//alert((cell && cell.innerHTML) + '/' + value)
	value = stripTags(value);
	if (link) {
		findLastChild(cell);
		if (last_el) last_el.firstChild.nodeValue = value
		else ac(ct(value), cell)
	}
	else resetValue()
	function findLastChild(base) {
		if (base && base.hasChildNodes()) {
			var list = base.childNodes, el;
			for (var i = 0, l = list.length; i < l; i ++) {
				el = list[i]
				if (el.nodeType == 3) last_el = base;
				findLastChild(el)
			}
		}
	}
	function resetValue() {
		if (cell && cell.hasChildNodes()) {
			var list = cell.childNodes, i = list.length;
			while (i --) cell.removeChild(list[i])
		}
		ac(ct(value), cell)
	}
}

addLoadEvent(addCounter)

function addCounter() {
	var box = gi('rating_mail_ru')
	if (box) box.innerHTML = topMailRu();
	
}

function topMailRu() {
	var cdict = dict.config.rating_mail_ru, a = '', r0 = (document.referrer), r = escape(r0), j = navigator.javaEnabled() || '', 
		js = 13, s = window.screen || {}, d = s.colorDepth || s.pixelDepth;
	if (!cdict) return '';
	s = (d) ? (s.width + '*' + s.height) : '';
	a += ';r=' + r + ';j=' + j + ';s=' + s + ';d=' + d;
	return ('<a href="http://top.mail.ru/jump?from=' + cdict.id + '" target="_top">'
	 + '<img src="' + cdict.src + ';t=' + cdict.theme + ';js='
	 + js + a + ';rand=' + Math.random() + '" alt="Рейтинг@Mail.ru"></a>');
}
