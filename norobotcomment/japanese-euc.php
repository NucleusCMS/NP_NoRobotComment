<?php
define('_NOROBOTCOMMENT_Description','独自の<input ... />タグを出力して、コメントフォームやメンバーメールフォームを悪用したスパムを拒否。');
// Option Settings
define('_NOROBOTCOMMENT_Label','出力タグ');
define('_NOROBOTCOMMENT_Mode','チェックボックス');
define('_NOROBOTCOMMENT_Mode0','チェックされていない場合に拒否');
define('_NOROBOTCOMMENT_Mode1','チェックされている場合に拒否');
define('_NOROBOTCOMMENT_Mode2','使用しない');
define('_NOROBOTCOMMENT_Error0','チェックボックスの表示メッセージ（チェックされていない場合）');
define('_NOROBOTCOMMENT_Error1','チェックボックスの表示メッセージ（チェックされている場合）');
define('_NOROBOTCOMMENT_Unchecked','チェックしてください');
define('_NOROBOTCOMMENT_Checked','チェックを外してください');
define('_NOROBOTCOMMENT_Message','確認メッセージ');
define('_NOROBOTCOMMENT_CheckMessage','チェックボックスを確認してください');

define('_NOROBOTCOMMENT_MinTimer','最短許可時間[秒]（「0」にすると無効になります）');
define('_NOROBOTCOMMENT_MaxTimer','最長許可時間[秒]（「0」にすると無効になります）');

define('_NOROBOTCOMMENT_Mail','メンバーメールフォームでもチェックボックスを表示しますか？');
define('_NOROBOTCOMMENT_BlogNumber','無効にするブログのid（複数入力する場合は「,」で区切ってください）');
define('_NOROBOTCOMMENT_Debug','管理操作履歴にログを記録しますか？');

define('_NOROBOTCOMMENT_EnglishRefused','半角英数字のみのコメントを拒否しますか？');
define('_NOROBOTCOMMENT_Error2','拒否時の表示メッセージ（半角英数字拒否の場合）');
define('_NOROBOTCOMMENT_LangMessage','Sorry... this site Japanese only.');

define('_NOROBOTCOMMENT_MailForm','メンバーメールフォーム');
define('_NOROBOTCOMMENT_EnglishComment','半角英数字のみのコメントです');
define('_NOROBOTCOMMENT_NotValid','正しくない値が送信されました');
define('_NOROBOTCOMMENT_TooFast','最短許可時間より短い時間で投稿されました');
define('_NOROBOTCOMMENT_TooLate','最長許可時間より長い時間で投稿されました');
