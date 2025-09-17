<?php 
  
    include('functions.php'); 
    add();
    include(HEADER_TEMPLATE);
    include(INIT);
    

  /*if (!isset($_SESSION)) session_start();
    if (isset($_SESSION['user'])) { // Verifica se tem um usuário logado
        if ($_SESSION['user'] != "mazi") {
            $_SESSION["message"] = "Você precisa ser administrador para acessar esse recurso!";
            $_SESSION['type'] = "danger";
            header("Location:" . BASEURL ."index.php");
        }
    } else {
        $_SESSION["message"] = "Você precisa estar logado e ser administrador para acessar esse recurso!";
        $_SESSION["type"] = "danger";
    }


  include(HEADER_TEMPLATE); */
  
?>
            <?php// if (!empty($_SESSION['message'])) : ?>
                <!--<div class="alert alert-<?php// echo $_SESSION['type']; ?> alert-dismissible" role="alert">
                    <?php //echo $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>-->
            <?php //else : ?>

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

        @media (max-width: 900px) {
            .form-group {
                margin: 0px auto;
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
                    <div class="col-md-6">
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
                    <div class="col-md-6 d-flex gap-2">
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
                        <div class="form-group  mb-3">
                            <label for="p_foto">Foto</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="p_foto" name="p_foto">
                                <button class="btn btn-light text-secondary" type="button" onclick="limparCaminho()"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div> 
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-2 mt-1">
                        <label for="imgPreview">Pré-visualização</label>
                        <img class="form-control shadow p-2 mb-2 bg-body rounded" id="imgPreview" src="imagens/noimg.jpg">
                    </div>
                </div>
                
                <div class="row justify-content-center">
                    <div id="actions" class="col-md-6 mt-3">
                        <div class="d-flex justify-content-center gap-2">
                            <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-file-circle-check"></i> Salvar</button>
                            <a href="index.php" class="btn btn-light text-dark"><i class="fa-solid fa-eraser"></i> Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
            <?php// endif;?>

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