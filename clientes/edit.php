<?php 
    include('functions.php'); 
    edit($_GET["id"]);
    include(HEADER_TEMPLATE);
    include(INIT);
?>

<section class="clientes section-light section-cor3 py-5" id="clientes">
    <div class="container mt-5" style="margin-top: 6rem !important;">
            <h2  class="txt1 mb-1 text-center">Editação de Cliente</h2>
            <p class="txt4 text-center mb-2"> Edite as informações de um Cliente e o mantenha atualizado.</p>
            <hr>

            <form action="edit.php?id=<?php echo $cli['id']; ?>" method="post" enctype="multipart/form-data">
                <!-- area de campos do form -->
                <div class="row justify-content-center">
                    <div class="col-md-10 d-flex gap-2">
                        <div class="form-group mb-3">
                            <label for="cli_nome">Nome</label>
                            <input type="text" class="form-control" id="cli_nome" name="cli[cli_nome]" value="<?php echo $cli['cli_nome']; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="cli_num">Número de Telefone</label>
                            <input type="number" class="form-control" id="cli_num" name="cli[cli_num]" value="<?php echo $cli['cli_num']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-10 d-flex gap-2">
                        <div class="col-md-5 d-flex gap-2">
                            <div class="form-group mb-3">
                                <label for="cli_cpf">CPF</label>
                                    <input type="text" class="form-control text-center" id="cli_cpf" name="cli[cli_cpf]" value="<?php echo $cli['cli_cpf']; ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="cli_nasc">Data de Nascimento</label>
                                <input type="datetime" class="form-control text-center" id="cli_nasc" name="cli[cli_nasc]" value="<?php echo $cli['cli_nasc']; ?>">
                            </div>
                        </div>
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