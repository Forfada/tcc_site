// ----------------------
// Formatação de telefone
// ----------------------
function formatarTelefone(input) {
    input.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, ''); // remove tudo que não é número
        if(v.length > 11) v = v.slice(0, 11); // limita a 11 dígitos

        if(v.length === 0) {
            e.target.value = '';
        } else if(v.length <= 2){
            e.target.value = '(' + v;
        } else if(v.length <= 7){
            e.target.value = '(' + v.slice(0,2) + ') ' + v.slice(2);
        } else {
            e.target.value = '(' + v.slice(0,2) + ') ' + v.slice(2,7) + '-' + v.slice(7);
        }

        // Validação mínima: 11 dígitos
        if(v.length < 11){
            input.setCustomValidity("O telefone deve ter exatamente 11 dígitos.");
        } else {
            input.setCustomValidity(""); // campo válido
        }
    });
}

// Remove a formatação antes de enviar o formulário
function limparTelefoneAntesDeEnviar(form) {
    form.addEventListener('submit', function(e){
        const telefoneInput = form.querySelector('.telefone');
        if(telefoneInput){
            telefoneInput.value = telefoneInput.value.replace(/\D/g,''); // envia só números
        }
    });
}

// ----------------------
// Formatação de nome
// ----------------------
function formatarNome(input) {
    input.addEventListener('input', function(e) {
        // Remove tudo que não for letra (A-Z, a-z) ou espaço
        e.target.value = e.target.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
    });
}

// ----------------------
// Inicialização
// ----------------------
function inicializarCampos() {
    document.querySelectorAll('.telefone').forEach(function(input) {
        if(!input.dataset.formatado){
            formatarTelefone(input);
            input.dataset.formatado = true;
        }
    });

    document.querySelectorAll('.nome').forEach(function(input) {
        if(!input.dataset.formatadoNome){
            formatarNome(input);
            input.dataset.formatadoNome = true;
        }
    });

    // Configura limpeza de telefone antes do envio
    document.querySelectorAll('form').forEach(function(form){
        if(!form.dataset.limpezaTelefone){
            limparTelefoneAntesDeEnviar(form);
            form.dataset.limpezaTelefone = true;
        }
    });
}

document.addEventListener('DOMContentLoaded', inicializarCampos);

// Observa mudanças no DOM (inputs carregados dinamicamente)
const observer = new MutationObserver(inicializarCampos);
observer.observe(document.body, { childList: true, subtree: true });
