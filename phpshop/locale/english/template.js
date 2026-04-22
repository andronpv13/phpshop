// Locale
var locale = {
    charset: "windows-1251",
    commentList: {
        mesHtml: "the function of adding comments is only available for authorized users.\n<a href='/users/?from=true'>Log in or register</a>.",
        mesSimple: "the function of adding comments is only available for authorized users.\log in or register.",
        mes: "Your comment will only be available to other users after passing moderation..."
    },
    OrderChekJq: {
        badReqEmail: "Please enter a valid email address",
        badReqName: "Please note that the \ name must consist of at least 3 letters",
        badReq: "Please note that all fields must be filled in",
        badDelivery: "Please select delivery"
    },
    commentAuthErrMess: "Only an authorized user can add a comment.\n<a href='" + ROOT_PATH + "/users/?from=true'>Please log in or register</a>.",
    incart: "in the trash",
    cookie_message: "in order to provide the most efficient service, this site uses cookies. By using this site, you consent to our use of cookies.",
    show: "Show",
    hide: "Hide",

};

$().ready(function () {
    locale_def = locale;
});