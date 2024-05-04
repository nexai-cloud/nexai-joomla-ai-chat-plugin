<?php
/**
* @package		Nexai Chat Integration for Joomla!
* @type		    Plugin (System)
* @filename	  nexai_chat.php
* @folder		  <root>/plugins/system/nexai_chat
* @version    0.0.1
* @modified		May 3rd 20203
* @author		  nexai.site / Nexai
* @website		https://nexai.site
* @url        https://nexai.site
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*
* @copyright (C) 2023-2023 nexai.site
*
* This program can be used under the terms of the GNU General Public License
* as published by the Free Software Foundation, either version 3 of the License.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\User;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;
use Joomla\CMS\HTML\HTMLHelper;

class plgSystemNexai_Chat extends CMSPlugin
{
	protected $app;
	protected $runApp=0;
	
	function onBeforeRender()
	{
    if($this->app->isClient('site'))
		{
			$this->runApp=1;
		}
	}
		
	public function onAfterRender()
  {
    if (!$this->runApp) {
        return;
    }

    // Get the user object
    $user = JUser::getInstance();

    // Initialize variables for user name, email, and avatar URL
    $userName = '';
    $userEmail = '';
    $userAvatarUrl = '';

    // Check if the user is logged in
    if (!$user->guest) {
        // Set the user's name and email
        $userName = $user->name;
        $userEmail = $user->email;
        
        // Check if the user avatar is available
        if (!empty($user->avatar)) {
            // Set the user's avatar URL
            $userAvatarUrl = JUri::root() . $user->avatar;
        }
    }

    $buffer = $this->app->getBody();

    $jsBuffer = '';
    $jsBuffer .= "<script src=\"https://nexai.site/ai/embed.umd.js\"></script>\n";
    $jsBuffer .= "<script>\n";
    $jsBuffer .= "// edit the configurations for your website\n";
    $jsBuffer .= "NexaiChatBubble.render({\n";
    $jsBuffer .= "  bottom: " . (int)$this->params->get('bottom', 30) . ",\n";
    $jsBuffer .= "  right: " . (int)$this->params->get('right', 30) . ",\n";
    $jsBuffer .= "  width: " . (int)$this->params->get('width', 400) . ",\n";
    $jsBuffer .= "  nexaiApiKey: '" . StringHelper::trim($this->params->get('nexaiApiKey', '')) . "',\n";
    $jsBuffer .= "  aiName: '" . StringHelper::trim($this->params->get('aiName', 'AI Assistant')) . "',\n";
    $jsBuffer .= "  aiAvatarUrl: '" . StringHelper::trim($this->params->get('aiAvatarUrl', '')) . "',\n";
    $jsBuffer .= "  projectName: '" . StringHelper::trim($this->params->get('projectName', 'Chat Support')) . "',\n";
    $jsBuffer .= "  inputPlaceholder: '" . StringHelper::trim($this->params->get('inputPlaceholder', '')) . "',\n";
    $jsBuffer .= "  chatSuggests: " . $this->params->get('chatSuggests', '[]') . ",\n"; 
    $jsBuffer .= "  userName: '" . $userName . "',\n";
    $jsBuffer .= "  userEmail: '" . $userEmail . "',\n";
    $jsBuffer .= "  userAvatarUrl: '" . $userAvatarUrl . "'\n";
    $jsBuffer .= "});\n";
    $jsBuffer .= "</script>\n";

    $buffer = str_replace("</body>", $jsBuffer . "</body>", $buffer);
    $this->app->setBody($buffer);
  }

}