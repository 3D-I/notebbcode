
Description:

Adds a Note BBCode, which displays a tooltip-like box containing text when the mouse is moved over the Note BBCode icon. The text inside the box can accept BBCodes, and the pop-up box will close automatically when the mouse is moved off the icon (unless the user clicks the icon, which will make the pop-up box stick in place until manually closed or until the cursor is moved a certain distance away from the pop-up box). Parses also smilies. BBcodes are automatically installed by the extension, no need to any manual input.

---------------------------------------------------
If by mistake you deleted manually this bbcode (note and note=) you can add them back following these instructions.
--------------------------------------------------
Go to the Administration Control Panel and navigate to the "Posting" tab, "BBCodes" section.
Click the "Add a new BBCode" button. The following page is where you are to enter the information for the new BBCode.

	(1) In the "BBCode usage" area, enter the following:
	
	[note]{TEXT}[/note]

	(2) In the "HTML replacement" area, enter the following:
	
	<span class="prime_bbcode_note_spur" onmouseover="show_note(this);" onmouseout="hide_note(this);" onclick="lock_note(this);"></span><span class="prime_bbcode_note">{TEXT}</span>

	(3) In the "Help line" area, enter the following:
	
	[note]note text[/note]

	(4) In the "Settings" area, check the box for "Display on posting page".

	When that's all filled out, click "Submit". 

Now, we're going to create one more version of this BBCode, so create a new BBCode and enter this information:

	(1) In the "BBCode usage" area, enter the following:
	
	[note={TEXT1}]{TEXT2}[/note]

	(2) In the "HTML replacement" area, enter the following:
	
	<span class="prime_bbcode_note_text" onmouseover="show_note(this);" onmouseout="hide_note(this);" onclick="lock_note(this);">{TEXT1}</span><span class="prime_bbcode_note_spur" onmouseover="show_note(this);" onmouseout="hide_note(this);" onclick="lock_note(this);"></span><span class="prime_bbcode_note">{TEXT2}</span>

	(3) In the "Help line" area, enter the following:
	
	[note=text-to-note]note text[/note]

	(4) In the "Settings" area, do NOT check the box for "Display on posting page". Well, you can
	if you want, but we already have the previous version of the tag being displayed.

When that's all filled out, click "Submit".

-------------------------------------------
