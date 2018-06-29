$(document).ready(function () {

    $('.button-collapse').sideNav({
        menuWidth: 250 // Default is 240
    });

    var s = location.pathname,
        doc = s.substr(s.lastIndexOf("/") + 1),
        $liIm = $("#li-im");

    switch (doc) {
        case "profile.php":
            $("#li-profile").addClass("active");
            break;
        case "chat.php":
            $liIm.addClass("active");
            break;
        case "find_contacts.php":
            $("#li-find-contacts").addClass("active");
            break;
        case "friends.php":
            $("#li-friends").addClass("active");
            break;
        case "im.php":
            $liIm.addClass("active");
            break;
        case "chat-all.php":
            $("#li-im-all").addClass("active");
            break;
        default:
            break;
    }
});
