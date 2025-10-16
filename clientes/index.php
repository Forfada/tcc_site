<?php
    include 'functions.php';
    include(INIT);
    include(HEADER_TEMPLATE);
    index();
?>  

<section class="clientes section-light section-cor3 py-5" id="clientes">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <h2 class="txt1 mb-1 text-center">Clientes Cadastrados</h2>

        <form name="filtro" action="index.php" method="post">
            <div class="row align-items-center"> 
                <div class="form-group col-12 col-md-5 mb-2 mb-md-0">
                    <div class="input-group">
                        <input type="text" class="form-control" maxlength="50" name="cli" placeholder="Procurar Cliente...">
                    <button type="submit" class="btn text-white" style="background-color: var(--cor7)"><i class="fa-solid fa-magnifying-glass"></i> Consultar</button>
                    </div>
                </div>
                <div class="col-12 col-md-7 text-md-end mt-2 mt-md-0">
                    <a class="buttonc" href="add.php" style="text-decoration: none"><i class="fa fa-plus"></i> Cadastrar Cliente </a>
                </div> 
            </div>
		</form>

		<hr>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th width="30%">Nome</th>
                        <th>Número</th>
                        <th>Nascimento</th>
                        <th width="26%"> Opções</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($clientes) : ?>
                    <?php foreach ($clientes as $cli) : ?>
                    <tr>
                        <td><?php echo $cli["id"]; ?></td>
                        <td><?php echo $cli["cli_nome"]; ?></td>
                        <td><?php echo $cli["cli_num"]; ?></td>
                        <td><?php echo $cli["cli_nasc"]; ?></td>
                        
                        <td class="actions text-center text-md-end d-flex flex-column gap-2">
                            <a href="view.php?id=<?php echo $cli['id']; ?>" class="btn btn-sm btn-dark d-flex align-items-center justify-content-center w-auto"><i class="fa fa-eye  me-2"></i> Visualizar</a>
                            <a href="edit.php?id=<?php echo $cli['id']; ?>" class="btn btn-sm btn-secondary d-flex align-items-center justify-content-center w-auto"><i class="fa-solid fa-pen-to-square  me-2"></i> Editar</a>
                            <a href="#" class="btn btn-sm btn-light d-flex align-items-center justify-content-center w-auto" 
                            data-bs-toggle="modal" data-bs-target="#delete-cli-modal" data-clientes="<?php echo $cli['id'];?>">
                                <i class="fa fa-trash  me-2"></i> Excluir
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="6">Nenhum registro encontrado.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include("modal.php"); ?>
<?php include(FOOTER_TEMPLATE); ?>