    <script src="<?php echo BASEURL; ?>js/bootstrap/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Auto-focus and format code input
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.querySelector('input[name="codigo"]');
            if (input) {
                input.focus();
                input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                });
            }
        });
    </script>
</body>
</html>