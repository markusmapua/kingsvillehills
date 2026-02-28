$(document).ready(function() {
        
    // Check for the user's sidebar preference
    var sidebarState = localStorage.getItem('sidebarState');

    // Default is closed if not set
    if (sidebarState === null) {
        sidebarState = 'closed';
        localStorage.setItem('sidebarState', 'closed');
    }

    // Loads the state on page load
    if (sidebarState === 'closed') {
        $("body").addClass("sidebar-toggled");
        $(".sidebar").addClass("toggled");
    } else {
        $("body").removeClass("sidebar-toggled");
        $(".sidebar").removeClass("toggled");
    }

    // Toggle the sidebar and save the state when the toggle button is clicked
    $("#sidebarToggle, #sidebarToggleTop").on('click', function() {

        setTimeout(function() {
            if ($("body").hasClass("sidebar-toggled")) {
                localStorage.setItem('sidebarState', 'closed'); // Save as closed
            } else {
                localStorage.setItem('sidebarState', 'open');   // Save as open
            }
        }, 100);
        
    });
});