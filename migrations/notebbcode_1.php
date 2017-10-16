<?php
/**
*
* @package phpBB Extension - notebbcode
* @copyright (c) 2016, 2017 3Di (Marco T.)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
* (migration based on the hard work of Matt Friedman/VSE)
*/

namespace threedi\notebbcode\migrations;

class notebbcode_1 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v3111');
	}

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'install_notebbcodes'))),
		);
	}

	public function revert_data()
	{
		return array(
			array('custom', array(array(&$this, 'notebbcodes_behind'))),
		);
	}

	/**
	 * notebbcodes left behind, hides the bbcode buttons on posting
	 *
	 * @param array $bbcode_tags Array of noteBBCodes tags to hide
	 * @return null
	 * @access public
	 */
	public function notebbcodes_behind($bbcode_tags)
	{
		/**
		 * @var array An array of notebbcodes (tags) to be left behind
		 */
		$bbcode_tags = array('note', 'note=');

		// set to null the display on posting
		$sql = 'UPDATE ' . BBCODES_TABLE . '
			SET display_on_posting = 0
			WHERE ' . $this->db->sql_in_set('bbcode_tag', $bbcode_tags);
			$this->db->sql_query($sql);
	}

	/**
	 * Installs BBCodes, used by migrations to perform add/updates
	 *
	 * @param array $bbcode_data Array of BBCode data to install
	 * @return null
	 * @access public
	 */
	public function install_notebbcodes($bbcode_data)
	{
		// Load the acp_bbcode class
		if (!class_exists('acp_bbcodes'))
		{
			include($this->phpbb_root_path . 'includes/acp/acp_bbcodes.' . $this->php_ext);
		}

		$bbcode_tool = new \acp_bbcodes();

		if (phpbb_version_compare(PHPBB_VERSION, '3.1.11', '>=') && phpbb_version_compare(PHPBB_VERSION, '3.2.0@dev', '<'))
		{
			/**
			 * @var array An array of bbcodes data to install 3.1
			 */
			$bbcode_data = array(
				'note' => array(
					'bbcode_match'		=> '[note]{TEXT}[/note]',
					'bbcode_tpl'		=> '<span class="prime_bbcode_note_spur" onmouseover="show_note(this);" onmouseout="hide_note(this);" onclick="lock_note(this);"></span><span class="prime_bbcode_note">{TEXT}</span>',
					'bbcode_helpline'	=> '[note]note text[/note]',
					'display_on_posting'=> 0,
				),
				'note=' => array(
					'bbcode_match'		=> '[note={TEXT1}]{TEXT2}[/note]',
					'bbcode_tpl'		=> '<span class="prime_bbcode_note_text" onmouseover="show_note(this);" onmouseout="hide_note(this);" onclick="lock_note(this);">{TEXT1}</span><span class="prime_bbcode_note_spur" onmouseover="show_note(this);" onmouseout="hide_note(this);" onclick="lock_note(this);"></span><span class="prime_bbcode_note">{TEXT2}</span>',
					'bbcode_helpline'	=> '[note=text-to-note]note text[/note]',
					'display_on_posting'=> 0,
				),
			);
		}
		else
		{
			/**
			 * @var array An array of bbcodes data to install 3.2
			 */
			$bbcode_data = array(
				'note' => array(
					'bbcode_match'		=> '[note text={TEXT1;optional}]{TEXT2}[/note]',
					'bbcode_tpl'		=> '<xsl:if test="@text"><span class="prime_bbcode_note_text" onmouseover="show_note(this);" onmouseout="hide_note(this);" onclick="lock_note(this);"><xsl:value-of select="@text"/></span></xsl:if><span class="prime_bbcode_note_spur" onmouseover="show_note(this);" onmouseout="hide_note(this);" onclick="lock_note(this);"/><span class="prime_bbcode_note"><xsl:apply-templates/></span>',
					'bbcode_helpline'	=> '[note]note text[/note] - OR - [note=note text]note text[/note]',
					'display_on_posting'=> 0,
				),
			);
		}

		foreach ($bbcode_data as $bbcode_name => $bbcode_array)
		{
			// Build the BBCodes
			$data = $bbcode_tool->build_regexp($bbcode_array['bbcode_match'], $bbcode_array['bbcode_tpl'], $bbcode_array['bbcode_helpline']);

			$bbcode_array += array(
				'bbcode_tag'			=> $data['bbcode_tag'],
				'first_pass_match'		=> $data['first_pass_match'],
				'first_pass_replace'	=> $data['first_pass_replace'],
				'second_pass_match'		=> $data['second_pass_match'],
				'second_pass_replace'	=> $data['second_pass_replace']
			);

			$sql = 'SELECT bbcode_id
				FROM ' . BBCODES_TABLE . "
				WHERE LOWER(bbcode_tag) = '" . $this->db->sql_escape(strtolower($bbcode_name)) . "'
				OR LOWER(bbcode_tag) = '" . $this->db->sql_escape(strtolower($bbcode_array['bbcode_tag'])) . "'";
			$result = $this->db->sql_query($sql);
			$row_exists = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row_exists)
			{
				// Update existing BBCode
				$bbcode_id = $row_exists['bbcode_id'];

				$sql = 'UPDATE ' . BBCODES_TABLE . '
					SET ' . $this->db->sql_build_array('UPDATE', $bbcode_array) . '
					WHERE bbcode_id = ' . (int) $bbcode_id;
				$this->db->sql_query($sql);
			}
			else
			{
				// Create new BBCode
				$sql = 'SELECT MAX(bbcode_id) AS max_bbcode_id
					FROM ' . BBCODES_TABLE;
				$result = $this->db->sql_query($sql);
				$max_bbcode_id = $this->db->sql_fetchfield('max_bbcode_id');
				$this->db->sql_freeresult($result);

				if ($max_bbcode_id)
				{
					$bbcode_id = $max_bbcode_id + 1;

					// Make sure it is greater than the core BBCode ids...
					if ($bbcode_id <= NUM_CORE_BBCODES)
					{
						$bbcode_id = NUM_CORE_BBCODES + 1;
					}
				}
				else
				{
					$bbcode_id = NUM_CORE_BBCODES + 1;
				}

				if ($bbcode_id <= BBCODE_LIMIT)
				{
					$bbcode_array['bbcode_id'] = (int) $bbcode_id;
					$bbcode_array['display_on_posting'] = ((int) $bbcode_array['display_on_posting']);
					$this->db->sql_query('INSERT INTO ' . BBCODES_TABLE . ' ' . $this->db->sql_build_array('INSERT', $bbcode_array));
				}
			}
		}
	}
}
