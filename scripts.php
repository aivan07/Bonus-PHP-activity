    <!-- Bootstrap JS Bundle -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <script>
        // Close the mobile menu after a link is tapped
        (function () {
            var nav = document.getElementById("mainNav");
            document.querySelectorAll("#mainNav a").forEach(function (link) {
                link.addEventListener("click", function () {
                    if (nav.classList.contains("show")) {
                        bootstrap.Collapse.getOrCreateInstance(nav).hide();
                    }
                });
            });
        })();
    </script>
