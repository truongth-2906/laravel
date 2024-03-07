const sidebar = {
    init: () => {
        $(document).on("click", "#sidebar-mobile button", () => {
            sidebar.toggleSidebar();
        });
    },

    toggleSidebar: () => {
        let width = document.getElementById("sidebar").style.width;

        if (width === '') {
            document.getElementById("sidebar").style.width = "280px";
            document.getElementById("sidebar").style.padding = "0 16px";
        } else {
            document.getElementById("sidebar").style.width = "";
            document.getElementById("sidebar").style.padding = "";
        }
    },
}

sidebar.init();
