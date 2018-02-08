var bubble = { "timer_id" : 0, "obj" : false, "timeout" : 300, "lock" : false };

function find_pos(obj)
{
	var x = 0, y = 0, w = obj.offsetWidth, h = obj.offsetHeight;
	while (obj.offsetParent)
	{
		x += obj.offsetLeft, y += obj.offsetTop;
		obj = obj.offsetParent;
	}
	if (!(obj.offsetParent))
	{
		x += (obj.x ? obj.x : 0), y += (obj.y ? obj.y : 0);
	}
	return new Array(x,y,w,h);
}


function get_next_sibling(obj, class_name)
{
	while ((obj = obj.nextSibling))
	{
		if (typeof(class_name) != "undefined")
		{
			if (obj.className == class_name)
			{
				return(obj);
			}
		}
		else
		{
			if (obj.nodeType != 1)
			{
				return(obj);
			}
		}
	}
	return(false);
}

function show_note(icon_obj)
{
	if (icon_obj && icon_obj.className != 'prime_bbcode_note_spur')
	{
		icon_obj = get_next_sibling(icon_obj, 'prime_bbcode_note_spur');
	}
	if (bubble.lock || typeof(icon_obj) != "undefined")
	{
		clearTimeout(bubble.timer_id);
	}
	if (!bubble.obj)
	{
		bubble.obj = document.getElementById("info_note");
	}
	if (bubble.obj && icon_obj)
	{
		var data_obj = get_next_sibling(icon_obj,"prime_bbcode_note");
		var position = find_pos(icon_obj);
		bubble.obj.innerHTML = '<span id="note-lock"><\/span><table><tr><td><a name="note-close" id="note-close" title="" onclick="close_note(); return false;"><\/a>' + data_obj.innerHTML + '<div class="min-width"><\/div><\/td><\/tr><\/table>';

		var close_icon = document.getElementById('note-close');
		if (close_icon)
		{
			close_icon.style.display = (bubble.lock ? 'block' : 'none');
			close_icon.style.left = '-5px';
		}
		bubble.obj.style.top  = ((position[1] - position[3]) + 5 - ((navigator.appName == 'Microsoft Internet Explorer') ? 0 : 5)) + 'px';
		bubble.obj.style.left = (position[0] + position[2] + 4) + 'px';
		bubble.obj.style.paddingLeft = 0;
		bubble.obj.style.paddingBottom = 0;
		bubble.obj.style.display = "block";
		icon_obj.title = "";
	}
}

function close_note()
{
	bubble.lock = false;
	if (bubble.obj)
	{
		bubble.obj.style.display = "none";
		bubble.obj.innerHTML = "";
		bubble.obj.style.top = 0;
		bubble.obj.style.left = 0;
		bubble.obj = null;
	}
}

function hide_note(icon_obj)
{
	bubble.timer_id = setTimeout("close_note()", (bubble.lock ? bubble.timeout : 2));
}

function lock_note(icon_obj)
{
	bubble.lock = !bubble.lock;
	var close_icon = document.getElementById('note-close');
	if (close_icon)
	{
		close_icon.style.display = (bubble.lock ? '' : 'none');
	}
	show_note(icon_obj)
}
