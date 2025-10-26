<?php
// Common verification page layout and styles
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?php echo BASEURL; ?>css/bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo BASEURL; ?>css/fontawesome/all.min.css">
<style>
    .verification-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #73213d, #9c2952);
        padding: 20px;
    }
    .verification-card {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 480px;
    }
    .verification-title {
        color: #73213d;
        margin-bottom: 1.5rem;
        font-size: 1.75rem;
        text-align: center;
    }
    .verification-input {
        letter-spacing: 0.5em;
        text-align: center;
        font-size: 1.5rem;
    }
    .btn-verificar {
        background: #73213d;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 500;
        width: 100%;
        margin-top: 1rem;
        transition: all 0.3s ease;
    }
    .btn-verificar:hover {
        background: #9c2952;
        transform: translateY(-2px);
        color: white;
    }
    .back-link {
        color: #73213d;
        text-decoration: none;
        margin-top: 1rem;
        display: inline-block;
        transition: color 0.3s ease;
    }
    .back-link:hover {
        color: #9c2952;
    }
</style>

<script>
// Common verification page JavaScript
document.addEventListener('DOMContentLoaded', function() {
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
    const input = document.querySelector('input[name="codigo"]');
    if (input) {
        input.focus();
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    }
});
</script>

<div class="verification-container">
    <div class="verification-card">
        <div class="verification-title">
            Verificação de Código
        </div>
        <form class="needs-validation" novalidate>
            <div class="mb-3">
                <input type="text" name="codigo" class="form-control verification-input" placeholder="Digite seu código" required>
                <div class="invalid-feedback text-center">
                    Por favor, digite o código de 6 dígitos.
                </div>
            </div>
            <button type="submit" class="btn btn-verificar">
                Verificar
            </button>
        </form>
        <a href="<?php echo BASEURL; ?>" class="back-link">
            Voltar para o início
        </a>
    </div>
</div>