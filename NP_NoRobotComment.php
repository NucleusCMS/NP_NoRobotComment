<?php
class NP_NoRobotComment extends NucleusPlugin {
    private $blognumbers = array();
    private $error ='';
    
    public function getName()        { return 'NoRobotComment'; }
    public function getAuthor()      { return 'pushman, Mocchi'; }
    public function getURL()         { return 'http://blog.heartfield-web.com/download/NP_NoRobotComment.html'; }
    public function getVersion()     { return '0.95'; }
    public function getDescription() { return _NOROBOTCOMMENT_Description; }
    
    public function getMinNucleusVersion()    { return 320; }
    public function supportsFeature($feature) { return in_array ($feature, array ('SqlTablePrefix', 'SqlApi'));}
    public function getEventList()            { return array('FormExtra', 'ValidateForm'); }
    
    public function init() { 
        $language = preg_replace('#[/|\\\\]#', '', getLanguageName());
        if (file_exists($this->getDirectory() . $language.'.php')) {
            include($this->getDirectory() . $language.'.php');
        } else {
            include($this->getDirectory() . 'english.php');
        }
        
        if ($this->getOption('blognumber')) {
            $blognumbers = explode(',', $this->getOption('blognumber'));
            foreach ($blognumbers as $blognumber) {
                $blognumber = trim($blognumber);
                if(!in_array($blognumber, $this->blognumbers)) {
                    array_push($this->blognumbers, $blognumber);
                }
            }
        }
        return;
    }
    
    public function install() {
        $this->createOption('mode', _NOROBOTCOMMENT_Mode, 'select', '0', $typeExtras = _NOROBOTCOMMENT_Mode0 . "|0|" . _NOROBOTCOMMENT_Mode1 . "|1|" . _NOROBOTCOMMENT_Mode2 . "|2");
        $this->createOption('tag', _NOROBOTCOMMENT_Label, 'textarea', '<input type="checkbox" name="ticket" value="<%ticket%>" id="norobot" /><label for="norobot"><%label%></label><input type="hidden" name="timer" value="<%timer%>" />');
        $this->createOption('label0', _NOROBOTCOMMENT_Error0, 'text', _NOROBOTCOMMENT_Unchecked);
        $this->createOption('label1', _NOROBOTCOMMENT_Error1, 'text', _NOROBOTCOMMENT_Checked);
        $this->createOption('checkmessage', _NOROBOTCOMMENT_Message, 'text', _NOROBOTCOMMENT_CheckMessage);
        $this->createOption('mintimer', _NOROBOTCOMMENT_MinTimer, 'text', '180', 'datatype=numerical');
        $this->createOption('maxtimer', _NOROBOTCOMMENT_MaxTimer, 'text', '1800', 'datatype=numerical');
        
        $this->createOption('mail', _NOROBOTCOMMENT_Mail, 'yesno', 'no');
        $this->createOption('blognumber', _NOROBOTCOMMENT_BlogNumber, 'text', '');
        $this->createOption('debug', _NOROBOTCOMMENT_Debug, 'yesno', 'no');
        
        $this->createOption('encomment', _NOROBOTCOMMENT_EnglishRefused, 'yesno', 'yes');
        $this->createOption('langmessage', _NOROBOTCOMMENT_Error2, 'text', _NOROBOTCOMMENT_LangMessage);
        
        return;
    }
    
    public function uninstall() {
        return;
    }
    
    public function event_FormExtra($data) {
        global $blogid, $errormessage, $member;
        
        if ($member->isLoggedIn() || in_array($blogid, $this->blognumbers) || $this->getOption('mode') == 2) {
            return;
        }
        
        if (!in_array($data['type'], array('commentform-notloggedin', 'commentform-loggedin'))
         && !($this->getOption('mail') == 'yes' && in_array($data['type'], array('membermailform-loggedin', 'membermailform-notloggedin')))) {
            return;
        }
        
        return $this->generator();
    }
    
    public function event_ValidateForm($data) {
        global $blogid;
        
        if (array_key_exists('member', $data) || in_array($blogid, $this->blognumbers)) {
            return;
        }
        
        $timer  = (integer) requestVar('timer');
        if (!$this->validator($timer)) {
            $data['error'] = $this->error;
            
            if ($this->getOption('debug') == 'yes') {
                if ($data['type'] == 'comment') {
                    $log = "[t] Item ID:{$data['comment']['itemid']} NANE:{$data['comment']['user']} | post value = \"{$timer}\" {$this->error}";
                } else {
                    $errorpage = _NOROBOTCOMMENT_MailForm;
                    $frommail  = requestVar('frommail');
                    $log = "[t] {$errorpage} E-mail:{$frommail} | post value = \"{$timer}\" {$this->error}";
                }
                $this->debugging($log);
            }
            
            return;
        }
        
        if ($this->getOption('encomment') == 'yes') {
            if ($data['type'] == 'comment') {
                $cbody = (string) requestVar('body');
                $iid = (integer) $data['comment']['itemid'];
                $bid = (integer) getBlogIDFromItemID($iid);
                $bname = getBlogNameFromID($bid);
                $inque = sprintf('SELECT ititle as result FROM %s WHERE inumber = %d', sql_table('item'), $iid);
                $iname = quickQuery($inque);
                $body= str_replace($iname, '', str_replace($bname, '', $cbody));
                $target = 'Item ID:' . $iid;
            } else if ($data['type'] == 'membermail') {
                $body = requestVar('message');
                $target = _NOROBOTCOMMENT_MailForm;
            }
            
            if ($body && $target && !$this->characterCheck($body, $target)) {
                $data['error'] = $this->error;
                
                if ($this->getOption('debug') == 'yes') {
                    $errortype = _NOROBOTCOMMENT_EnglishComment;
                    $this->debugging("[e] {$target} {$errortype}");
                }
            }
        }
        return;
    }
    
    public function generator() {
        global $manager, $DIR_LIBS;
        
        if (!class_exists('MANAGER', FALSE)) {
            include(preg_replace('#/*$#', '', $DIR_LIBS) . '/MANAGER.php');
        }
        
        if (!isset($manager) || 'MANAGER' !== get_class($manager)) {
            return FALSE;
        }
        
        $ticket = $manager->getNewTicket();
        $timer = time();
        $replacements = array(
            '<%ticket%>' => $ticket,
            '<%timer%>' => $timer
        );
        
        if ($this->getOption('mode') == 0) {
            $replacements['<%label%>'] = $this->getOption('label0');
        } else if ($this->getOption('mode') == 1) {
            $replacements['<%label%>'] = $this->getOption('label1');
            $replacements['type="checkbox"'] = 'type="checkbox" checked="checked"';
        }
        
        $tag = $this->getOption('tag');
        foreach ($replacements as $target => $replacement) {
            $tag = str_replace ($target, $replacement, $tag);
        }
        
        echo $tag;
    }
    
    public function validator ($posttime=0) {
        global $manager, $DIR_LIBS;
        
        if (!class_exists('MANAGER', FALSE)) {
            include(preg_replace('#/*$#', '', $DIR_LIBS) . '/MANAGER.php');
        }
        
        if (!isset($manager) || 'MANAGER' !== get_class($manager)) {
            return FALSE;
        }
        
        if (preg_match('/[^0-9]/', $posttime)) {
            $this->error = _NOROBOTCOMMENT_NotValid;
        } else if ($this->getOption('mintimer') != 0 && time() - $posttime < $this->getOption('mintimer')) {
            $this->error = _NOROBOTCOMMENT_TooFast;
        } else if ($this->getOption('maxtimer') != 0 && time() - $posttime > $this->getOption('maxtimer')) {
            $this->error = _NOROBOTCOMMENT_TooLate;
        } else if (($this->getOption('mode') == 0 && !$manager->checkTicket())
         || ($this->getOption('mode') == 1 && array_key_exists('ticket', $_POST))) {
            $this->error = $this->getOption('checkmessage');
        }
        
        return ($this->error == '');
    }
    
    private function characterCheck($body, $target) {
        $body = str_replace("\\", '', $body);
        
        if (!defined('_CHARSET')) {
            return TRUE;
        }
        
        if (_CHARSET == 'EUC-JP') {
            $encode = 'EUC-JP, UTF-8, SJIS, JIS, ASCII';
        } else {
            $encode = 'UTF-8, EUC-JP, SJIS, JIS, ASCII';
        }
        
        $body = mb_convert_encoding($body, 'UTF-8', $encode);
        if ($body && !preg_match('/[\x80-\xff]/', $body)) {
            $this->error = $this->getOption('langmessage');
        }
        
        return ($this->error == '');
    }
    
    private function debugging($log) {
        ACTIONLOG::add(INFO, 'NoRobotComment: ' . $log);
    }
}
