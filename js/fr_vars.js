/* ORIGINAL
// URL String
rootURL     = "http://csis.svsu.edu/";
userNameURL = "~gpcorser";
appURL      = "/fr/";
URL         = rootURL + userNameURL + appURL;
*/

// URL String
rootURL     = "http://www.cis355.com/";
userNameURL = "alpero/";
appURL      = "fr/";
URL         = rootURL + userNameURL + appURL;

// Get ID from URL
function getID() {
    id = window.location.search.substring(1);
    id = id.split("=");
    return id[1];
}