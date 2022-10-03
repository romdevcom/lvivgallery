<?php
define(BR, '<br>');
define(NL, "\n");
if ($_GET['r']) {
	$res = mail($_GET['r'], $_GET['s'] ? $_GET['s'] : 'Test mail', $_GET['m'] ? $_GET['m'] : 'Its a test mail');
	echo 'Message sent: '.($res ? 'yes' : 'no').BR.NL;
	echo '<hr>'.BR.NL;
}
echo '<form>'.BR.NL;
echo 'To: <input type="text" name="r" value="'.($_GET['r'] ? $_GET['r'] : '').'">'.BR.NL;
echo 'Subject: <input type="text" name="s" value="'.($_GET['s'] ? $_GET['s'] : 'Test mail').'">'.BR.NL;
echo 'Message: <input type="text" name="m" value="'.($_GET['m'] ? $_GET['m'] : 'Its a test mail').'">'.BR.NL;
echo '<input type="submit" value="Send">'.BR.NL;

echo '</form>'.BR.NL;
?>
