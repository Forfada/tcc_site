//Passa os dados do procedimento para o Modal, e atualiza o link para exclusão
$("#delete-proc-modal").on("show.bs.modal", function (event) {
    var button = $(event.relatedTarget);
    var id = button.data("procedimentos");

    var modal = $(this);
    modal.find(".modal-title").text("Excluir Procedimento: " + id);
    modal.find("#confirm").attr("href", "delete.php?id=" + id);
});

//Passa os dados do procedimento para o Modal, e atualiza o link para exclusão
$("#delete-cli-modal").on("show.bs.modal", function (event) {
    var button = $(event.relatedTarget);
    var id = button.data("clientes");

    var modal = $(this);
    modal.find(".modal-title").text("Excluir Cliente: " + id);
    modal.find("#confirm").attr("href", "delete.php?id=" + id);
});

