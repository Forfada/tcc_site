<?php 
    include('functions.php'); 

    // somente administrador pode acessar esta página
    if (!function_exists('is_admin') || !is_admin()) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['message'] = "Você não pode acessar essa funcionalidade.";
        $_SESSION['type'] = "danger";
        header("Location: " . BASEURL . "index.php");
        exit;
    }

    add();
    include(HEADER_TEMPLATE);
    include(INIT);
?>

    <style>
        .form-group{
            --bs-gutter-x: 1rem !important;
            width: 100%;
        }

        .form-group #imgPreview {
            width: 25%;
        }

        label {
            font-size: 1rem;
            }

        @media (max-width: 767px) {
            .form {
                flex-direction: column;
                display: flex;
                gap: 0;
            }
            .form-group {
                width: 100%;
                margin-left: 0;
                margin-top: 0.5rem;
            }
            .d-flex {
                flex-wrap: wrap;
            }
            .col-md-5.d-flex {
                flex-direction: row;
                flex-wrap: nowrap;
                width: 100%;
                gap: 0.5rem;
            }
            .img{
                width: 75%;
            }
        }
    </style>

    <section class="procedimentos section-light section-cor3 py-5" id="procedimentos">
        <div class="container mt-5" style="margin-top: 6rem !important;">
            <h2  class="txt1 mb-1 text-center">Novo Procedimento</h2>
            <p class="txt4 text-center mb-2"> Adicione um novo Procedimento à sua Clínica.</p>
            <hr>

            <form action="add.php" method="post" enctype="multipart/form-data">
                <!-- area de campos do form -->
                <div class="row justify-content-center">
                    <div class="col-md-10 d-flex gap-2 form">
                        <div class="form-group mb-3">
                            <label for="p_nome">Nome</label>
                            <input type="text" class="form-control" id="p_nome" name="proc[p_nome]" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="p_descricao">Descrição</label>
                            <input type="text" class="form-control" id="p_descricao" name="proc[p_descricao]" required>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-10 d-flex gap-2">
                        <div class="col-md-5 d-flex gap-2">
                            <div class="form-group mb-3">
                                <label for="p_valor">Valor</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control text-center" id="p_valor" name="proc[p_valor]" required>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="p_duracao">Duração</label>
                                <input type="time" class="form-control text-center" id="p_duracao" name="proc[p_duracao]" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="p_descricao">Explicação do Procedimento</label>
                            <textarea type="text" class="form-control" id="p_descricao" name="proc[p_descricao2]" rows=1 required></textarea>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-5 mt-1">
                        <div class="form-group mb-3">
                            <label for="p_foto">Foto</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="p_foto" name="p_foto">
                                <button class="btn btn-light text-secondary" type="button" onclick="limparCaminho()"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-2 mt-1 img">
                        <label for="imgPreview">Pré-visualização</label>
                        <img class="form-control shadow p-2 mb-2 bg-body rounded" id="imgPreview" src="imagens/noimg.jpg">
                    </div>
                </div>
                
                <div class="row justify-content-center">
                    <div id="actions" class="col-md-6 mt-3">
                        <div class="d-flex justify-content-center gap-2">
                            <button type="submit" class="buttonc"><i class="fa-solid fa-check"></i> Salvar</button>
                            <a href="index.php" class="buttona" style="text-decoration: none;"><i class="fa-solid fa-arrow-rotate-left"></i> Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

<?php include(FOOTER_TEMPLATE); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>  
    function limparCaminho() {
        // Limpar o valor do input
        document.getElementById('p_foto').value = '';

        // Exibir a foto original na pré-visualização
        document.getElementById('imgPreview').src = 'imagens/noimg.jpg';
    }

    $(document).ready(()=>{
      $('#p_foto').change(function(){
        const file = this.files[0];
        console.log(file);
        if (file){
          let reader = new FileReader();
          reader.onload = function(event){
            console.log(event.target.result);
            $('#imgPreview').attr('src', event.target.result);
          }
          reader.readAsDataURL(file);
        }
      });
    });
</script>