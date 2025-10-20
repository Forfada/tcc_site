// ----------------------
// Formatação de telefone
// ----------------------
function formatarTelefone(input) {
    input.addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, ''); // remove tudo que não é número
        if (v.length > 11) v = v.slice(0, 11); // limita a 11 dígitos

        if (v.length === 0) {
            e.target.value = '';
        } else if (v.length <= 2) {
            e.target.value = '(' + v;
        } else if (v.length <= 7) {
            e.target.value = '(' + v.slice(0, 2) + ') ' + v.slice(2);
        } else {
            e.target.value = '(' + v.slice(0, 2) + ') ' + v.slice(2, 7) + '-' + v.slice(7);
        }

        // Validação mínima: 11 dígitos
        if (v.length < 11) {
            input.setCustomValidity("O telefone deve ter exatamente 11 dígitos.");
        } else {
            input.setCustomValidity(""); // campo válido
        }
    });
}

// Remove a formatação antes de enviar o formulário
function limparTelefoneAntesDeEnviar(form) {
    form.addEventListener('submit', function () {
        const telefoneInput = form.querySelector('.telefone');
        if (telefoneInput) {
            telefoneInput.value = telefoneInput.value.replace(/\D/g, ''); // envia só números
        }
    });
}

// ----------------------
// Formatação de nome
// ----------------------
function formatarNome(input) {
    input.addEventListener('input', function (e) {
        // Remove tudo que não for letra (A-Z, a-z) ou espaço
        e.target.value = e.target.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
    });
}

// ----------------------
// Formatação de CPF
// ----------------------
function formatarCPF(input) {
    input.addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, ''); // remove tudo que não é número
        if (v.length > 11) v = v.slice(0, 11); // limita a 11 dígitos

        if (v.length === 0) {
            e.target.value = '';
        } else if (v.length <= 3) {
            e.target.value = v;
        } else if (v.length <= 6) {
            e.target.value = v.replace(/(\d{3})(\d{0,3})/, '$1.$2');
        } else if (v.length <= 9) {
            e.target.value = v.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
        } else {
            e.target.value = v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
        }

        // Validação mínima: 11 dígitos
        if (v.length < 11) {
            input.setCustomValidity("O CPF deve ter exatamente 11 dígitos.");
        } else {
            input.setCustomValidity(""); // campo válido
        }
    });
}

// Remove a formatação do CPF antes de enviar
function limparCPFAntesDeEnviar(form) {
    form.addEventListener('submit', function () {
        const cpfInput = form.querySelector('.cpf');
        if (cpfInput) {
            cpfInput.value = cpfInput.value.replace(/\D/g, ''); // envia só números
        }
    });
}

// ----------------------
// Inicialização
// ----------------------
function inicializarCampos() {
    document.querySelectorAll('.telefone').forEach(function (input) {
        if (!input.dataset.formatadoTelefone) {
            formatarTelefone(input);
            input.dataset.formatadoTelefone = true;
        }
    });

    document.querySelectorAll('.nome').forEach(function (input) {
        if (!input.dataset.formatadoNome) {
            formatarNome(input);
            input.dataset.formatadoNome = true;
        }
    });

    document.querySelectorAll('.cpf').forEach(function (input) {
        if (!input.dataset.formatadoCPF) {
            formatarCPF(input);
            input.dataset.formatadoCPF = true;
        }
    });

    // Configura limpeza antes do envio
    document.querySelectorAll('form').forEach(function (form) {
        if (!form.dataset.limpezaCampos) {
            limparTelefoneAntesDeEnviar(form);
            limparCPFAntesDeEnviar(form);
            form.dataset.limpezaCampos = true;
        }
    });
}

document.addEventListener('DOMContentLoaded', inicializarCampos);

// Observa mudanças no DOM (para inputs carregados dinamicamente)
const observer = new MutationObserver(inicializarCampos);
observer.observe(document.body, { childList: true, subtree: true });
