<?php
    include 'functions.php';
    // valida id recebido via GET (retorna int ou false)
    $cid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($cid === false || $cid === null || $cid <= 0) {
        // id inválido: redirecionar ou mostrar erro amigável
        header('Location: index.php'); // ou mostrar mensagem
        exit;
    }
    view($cid);
    $anamnese = index_an($cid);

    if (empty($cli)) {
        echo '<p>Cliente não encontrado.</p>';
        exit;
    };
    include(INIT);
    include(HEADER_TEMPLATE);
?>

<section class="clientes section-light section-cor3 py-5" id="clientes">
     <div class="container mt-5" style="margin-top: 6rem !important;">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="txt1"><?php echo $cli['cli_nome']; ?></h2>
                <div class="d-flex gap-2">
                    <a href="index.php" class="btn btn-dark btn-sm">&larr; Voltar</a>
                    <a href="edit.php?id=<?php echo $cli['id']; ?>" class="btn btn-secondary btn-sm">Editar</a>
                </div>
            </div>
        </div>

        <div class="container my-5">
				<div class="row my-5">
					<div class="col-12">
						
                        <div class="tabela-wrapper">
                            <table class="tabela-lunaris">
                                <thead>
                                    <tr>
                                    
                                    <th style="width:7%">Idade</th>
                                    <th style="width:17%">CPF</th>
                                    <th style="width:17%">Telefone</th>
                                    <th style="width:14%">Nascimento</th>
                                    <th style="width:45%">Observação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        
                                        <td><?php echo htmlspecialchars($cli['cli_idade']); ?></td>
                                        <td><?php echo cpf($cli['cli_cpf']); ?></td>
                                        <td><?php echo telefone($cli['cli_num']); ?></td>
                                        <td><?php echo formatadata($cli['cli_nasc']); ?></td>
                                        <td style="word-wrap: break-word; word-break: break-word;"><?php echo htmlspecialchars($cli['cli_obs']); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

					</div>	
				</div>
			</div>


        <div class="row">
            <div class="col-12">
                <div class="anamnese-panel p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Anamnese</h4>
                        <a href="add_an.php?client_id=<?php echo $cli['id']; ?>" class="buttonc" style="text-decoration: none"><i class="fa fa-plus"></i> Adicionar Anamnese</a>
                    </div>

                    <?php if (!empty($anamnese) && is_array($anamnese)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Hipertensão</th>
                                        <th>Diabetes</th>
                                        <th>Câncer</th>
                                        <th>Medicações</th>
                                        <th>Registrado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($anamnese as $a): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($a['an_hipertensao']); ?></td>
                                        <td><?php echo htmlspecialchars($a['an_diabetes']); ?></td>
                                        <td><?php echo htmlspecialchars($a['an_cancer']); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($a['an_medic'])); ?></td>
                                        <td><?php echo formatadata($a['an_data']); ?></td>
                                        <td>
                                            <a href="edit_an.php?anid=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                            <a href="delete_an.php?anid=<?php echo $a['id']; ?>&client_id=<?php echo $cli['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Confirma remoção desta anamnese?');">Excluir</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Não há anamnese cadastrada para este cliente.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .clientes table th {
        background-color: #ffffffff;
        font-weight: 600;
        white-space: nowrap;
    }

    .clientes table td, 
    .clientes table th {
        vertical-align: middle;
    }

    .clientes .table {
        border-color: #dee2e6;
        box-shadow: 0 6px 18px rgba(38,57,77,0.04);
    }

    .clientes .border {
        border-color: #e3e6ea !important;
    }

    .clientes .shadow-sm {
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05) !important;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .clientes .shadow-sm:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08) !important;
    }

    @media (max-width: 768px) {
        .clientes .p-4 {
            padding: 1.25rem !important;
        }
        .clientes table th,
        .clientes table td {
            font-size: 0.9rem;
        }
    }

    .avatar-placeholder {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #910dfdff 0%, #350715ff 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 2rem;
        margin: 0 auto;
        box-shadow: 0 6px 20px rgba(13,110,253,0.12);
    }

    .info-grid {
    display: flex;  /* Usa flexbox ao invés de grid */
    flex-wrap: wrap; /* Permite que os itens se ajustem para a próxima linha, se necessário */
    gap: 0.5rem 0.25rem; /* Reduz o espaço entre as colunas (horizontal) e entre as linhas (vertical) */
    
}

.info-grid > div {
    flex: 1 1 30%; /* Faz com que as colunas ocupem 30% do espaço disponível e quebrem para a linha seguinte quando necessário */
    margin-bottom: 0.5rem; /* Espaçamento inferior para as colunas */
}

.info-label, .info-value {
    display: block;
    font-size: 1rem;
    color: #212529;
}

.info-label {
    color: #6c757d;
    margin-bottom: .25rem;
}

.info-value {
    font-weight: 600;
}


    .anamnese-panel {
        background-color: transparent;
        padding: 1rem;
        
    }

.card{
    border: none;
}

/* ======= Tabela (novo visual elegante) ======= */
.tabela-wrapper {
  width: 100%;
  max-width: 1200px;
  background: linear-gradient(180deg, #fff, #fffaf9);
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 8px 28px rgba(35,20,30,0.08);
  overflow-x: auto;
  border: 1px solid rgba(115,33,61,0.06);
}

.tabela-lunaris {
  width: 100%;
  border-collapse: collapse;
  color: #2d2d2d;
  font-family: "Inter", "Poppins", system-ui, -apple-system, "Segoe UI", Roboto, Arial;
  background: transparent;
}

/* CENTRALIZA os nomes/células da tabela (header e corpo) */
.tabela-lunaris thead th,
.tabela-lunaris tbody td {
  text-align: center;
}

.tabela-lunaris thead th {
  background: linear-gradient(90deg, #fff8f7, #fff);
  color: #73213d;
  padding: 12px 14px;
  font-weight: 700;
  font-size: 0.95rem;
  border-bottom: 2px solid rgba(115,33,61,0.06);
}

.tabela-lunaris tbody td {
  padding: 12px 14px;
  border-bottom: 1px solid rgba(115,33,61,0.04);
  vertical-align: middle;
  color: #444;
  font-size: 0.95rem;
}

.tabela-lunaris tbody tr {
  background: transparent;
  transition: background 0.18s ease, transform 0.18s ease;
}

.tabela-lunaris tbody tr:hover {
  background: linear-gradient(90deg, rgba(115,33,61,0.03), rgba(160,90,111,0.02));
  transform: translateY(-2px);
}

.tabela-lunaris tbody tr:nth-child(even) {
  background: rgba(115,33,61,0.02);
}

.tabela-lunaris button {
  background: linear-gradient(180deg, #a05a6f, #73213d);
  border: none;
  color: #fff;
  border-radius: 8px;
  padding: 8px 12px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
}

/* ====== Remover exibição do tempo dentro do dropdown ======
   - oculta elementos que contenham atributo data-dur dentro do painel dropdown
   - oculta elementos com classe .proc-duration dentro do dropdown
   (se necessário, ajuste a classe conforme template de saída dos procedimentos)
*/
#dropdownContent [data-dur],
#dropdownContent .proc-duration,
.dropdown-item [data-dur] {
  display: none !important;
}

/* fallback: pequenos spans/elem de duração dentro do dropdown */
#dropdownContent .proc-meta small,
#dropdownContent .proc-meta .duracao {
  display: none !important;
}

/* ensure header cells don't look cramped on small screens */
@media (max-width: 768px) {
  .tabela-lunaris thead th, .tabela-lunaris tbody td { padding: 10px 8px; font-size: 0.9rem; }
  .tabela-wrapper { padding: 12px; }
} 
</style>

<?php include(FOOTER_TEMPLATE); ?>
