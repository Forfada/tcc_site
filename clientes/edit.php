<?php 
    include('functions.php'); 
    edit($_GET["id"]);
    include(HEADER_TEMPLATE);
    include(INIT);
?>
 <style>
        .form-group{
            --bs-gutter-x: 1rem !important;
            width: 100%;
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
        }
    </style>
<section class="clientes section-light section-cor3 py-5" id="clientes">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <h2  class="txt1 mb-1 text-center">Editação de Cliente</h2>
        <p class="txt4 text-center mb-2"> Edite as informações de um Cliente e o mantenha atualizado.</p>
        <hr>

        <form action="edit.php?id=<?php echo $cli['id']; ?>" method="post">
            <!-- area de campos do form -->
            <div class="row justify-content-center">
                <div class="col-md-5 d-flex justify-content-center gap-2 form">
                    <div class="form-group mb-3">
                        <label for="cli_nome">Nome</label>
                        <input type="text" class="form-control text-center" id="cli_nome" name="cli[cli_nome]" value="<?php echo $cli['cli_nome']; ?>">
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-1 d-flex justify-content-center gap-2 form">
                    <div class="form-group mb-3">
                        <label for="cli_idade">Idade</label>
                        <input type="number" class="form-control text-center" id="cli_idade" name="cli[cli_idade]" value="<?php echo $cli['cli_idade']; ?>">
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-center gap-2 form">
                    <div class="form-group mb-3">
                        <label for="cli_cpf">CPF</label>
                        <input type="text" class="form-control text-center" id="cli_cpf" name="cli[cli_cpf]" value="<?php echo $cli['cli_cpf']; ?>">
                    </div>
                    <div class="form-group mb-3">
                        <label for="cli_num">Número de Telefone</label>
                        <input type="tel" class="form-control text-center" id="cli_num" name="cli[cli_num]" value="<?php echo $cli['cli_num']; ?>">
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-3 d-flex justify-content-center gap-2 form">
                    <div class="form-group mb-3">
                        <label for="cli_nasc">Data de Nascimento</label>
                        <input type="datetime" class="form-control text-center" id="cli_nasc" name="cli[cli_nasc]" value="<?php echo formatadata($cli['cli_nasc']); ?>">
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