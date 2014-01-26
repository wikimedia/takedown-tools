
//logout from system, taken from a couple different places but especially http://stackoverflow.com/questions/233507/how-to-log-out-user-from-web-site-using-basic-authentication 
function logout(Loc){
    var outcome, u, m = 'You should be logged out now.';

    try { outcome = document.execCommand('ClearAuthenticationCache'); }catch(e){}

    if (!outcome) {

        outcome = (function(x){
            if (x) {
                x.open('HEAD', Loc || location.href, true, 'logout', (new Date()).getTime().toString());
                x.send('');

                return 1;
            } else {
                return;
            }
        })(window.XMLHttpRequest ? new window.XMLHttpRequest() : ( window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : u ));
    }
    if (!outcome) {
        m = 'Hmmm, looks like something went wrong, try closing your browser to fully log out';
    }
    alert(m);

}
