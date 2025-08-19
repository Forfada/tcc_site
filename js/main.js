//Passa os dados do paciente para o Modal, e atualiza o link para exclusão
$("#delete-modal").on("show.bs.modal", function (event) {
  
	var button = $(event.relatedTarget);
	var id = button.data("customer");
  
	var modal = $(this);
	modal.find(".modal-title").text("Excluir Paciente: " + id);
	modal.find("#confirm").attr("href", "delete.php?id=" + id);
});

//Passa os dados do enfermeiro para o Modal, e atualiza o link para exclusão
$("#delete-enf-modal").on("show.bs.modal", function (event) {
    var button = $(event.relatedTarget);
    var id = button.data("enfermeiros");

    var modal = $(this);
    modal.find(".modal-title").text("Excluir enfermeiro: " + id);
    modal.find("#confirm").attr("href", "delete.php?id=" + id);
});

//Passa os dados do usuario para o Modal, e atualiza o link para exclusão
$("#delete-user-modal").on("show.bs.modal", function (event) {
  
	var button = $(event.relatedTarget);
	var id = button.data("usuario");
  
	var modal = $(this);
	modal.find(".modal-title").text("Excluir Usuário: " + id);
	modal.find("#confirm").attr("href", "delete.php?id=" + id);
});