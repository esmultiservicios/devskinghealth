<script>
/*
|--------------------------------------------------------------------------
| INVOICES - Facturación
|--------------------------------------------------------------------------
| Este archivo valida la factura antes de enviarla al PHP.
| Respeta los names actuales:
| productoID[], productName[], quantity[], price[], discount[], total[]
|--------------------------------------------------------------------------
*/

(function () {
    "use strict";

    function toNumber(value) {
        value = $.trim(value);
        if (value === "" || value === null || typeof value === "undefined") {
            return 0;
        }

        value = String(value).replace(",", ".");
        var number = parseFloat(value);

        return isNaN(number) ? 0 : number;
    }

    function showFacturaError(message) {
        swal({
            title: "Error",
            text: message,
            icon: "error",
            dangerMode: true,
            closeOnEsc: false,
            closeOnClickOutside: false
        });
    }

    function getNextFacturaIndex() {
        var maxIndex = -1;

        $("#formulario_facturacion #invoiceItem tbody tr").each(function () {
            $(this).find("[id]").each(function () {
                var id = $(this).attr("id");
                var match = id ? id.match(/_(\d+)$/) : null;

                if (match) {
                    var currentIndex = parseInt(match[1], 10);
                    if (currentIndex > maxIndex) {
                        maxIndex = currentIndex;
                    }
                }
            });
        });

        return maxIndex + 1;
    }

    function facturaRowHtml(count) {
        var htmlRows = "";

        htmlRows += "<tr>";
        htmlRows += '<td><input class="itemRow" type="checkbox"></td>';

        htmlRows += "<td>";
        htmlRows += '<input type="hidden" name="isv[]" id="isv_' + count + '" class="form-control" placeholder="Producto ISV" autocomplete="off">';
        htmlRows += '<input type="hidden" name="valor_isv[]" id="valor_isv_' + count + '" class="form-control" placeholder="Valor ISV" autocomplete="off">';
        htmlRows += '<input type="hidden" name="facturas_detalle_id[]" id="facturas_detalle_id_' + count + '" class="form-control" placeholder="Código Producto" autocomplete="off">';
        htmlRows += '<input type="hidden" name="productoID[]" id="productoID_' + count + '" class="form-control" placeholder="Código Producto" autocomplete="off">';

        htmlRows += '<div class="input-group">';
        htmlRows += '<input type="text" name="productName[]" id="productName_' + count + '" class="form-control producto" placeholder="Producto o Servicio" autocomplete="off">';
        htmlRows += '<div id="suggestions_producto_' + count + '" class="suggestions"></div>';
        htmlRows += '<div class="input-group-append" id="grupo_buscar_productos_' + count + '">';
        htmlRows += '<a data-toggle="modal" href="#" class="btn btn-outline-success buscar_productos">';
        htmlRows += '<div class="sb-nav-link-icon"></div><i class="buscar_producto fas fa-search-plus fa-lg"></i>';
        htmlRows += "</a>";
        htmlRows += "</div>";
        htmlRows += "</div>";
        htmlRows += "</td>";

        htmlRows += '<td><input type="number" name="quantity[]" id="quantity_' + count + '" placeholder="Cantidad" class="buscar_cantidad form-control" min="1" step="1" autocomplete="off"></td>';

        htmlRows += '<td><input type="number" name="price[]" id="price_' + count + '" placeholder="Precio" class="form-control price" step="0.01" readonly autocomplete="off"></td>';

        htmlRows += "<td>";
        htmlRows += '<div class="input-group mb-3">';
        htmlRows += '<input type="number" name="discount[]" id="discount_' + count + '" class="form-control" step="0.01" placeholder="Descuento" readonly autocomplete="off">';
        htmlRows += '<div class="input-group-append" id="grupo_aplicar_descuento_' + count + '">';
        htmlRows += '<a data-toggle="modal" href="#" class="btn btn-outline-success">';
        htmlRows += '<div class="sb-nav-link-icon"></div><i class="aplicar_descuento fas fa-plus fa-lg"></i>';
        htmlRows += "</a>";
        htmlRows += "</div>";
        htmlRows += "</div>";
        htmlRows += "</td>";

        htmlRows += '<td><input type="number" name="total[]" id="total_' + count + '" placeholder="Total" class="form-control total" step="0.01" readonly autocomplete="off"></td>';

        htmlRows += "</tr>";

        return htmlRows;
    }

    function reindexFacturaRows() {
        $("#formulario_facturacion #invoiceItem tbody tr").each(function (index) {
            var row = $(this);

            row.find(".itemRow").prop("checked", false);

            row.find("[name='isv[]']").attr("id", "isv_" + index);
            row.find("[name='valor_isv[]']").attr("id", "valor_isv_" + index);
            row.find("[name='facturas_detalle_id[]']").attr("id", "facturas_detalle_id_" + index);
            row.find("[name='productoID[]']").attr("id", "productoID_" + index);
            row.find("[name='productName[]']").attr("id", "productName_" + index);
            row.find("[name='quantity[]']").attr("id", "quantity_" + index);
            row.find("[name='price[]']").attr("id", "price_" + index);
            row.find("[name='discount[]']").attr("id", "discount_" + index);
            row.find("[name='total[]']").attr("id", "total_" + index);

            row.find(".suggestions").attr("id", "suggestions_producto_" + index);
            row.find("[id^='grupo_buscar_productos']").attr("id", "grupo_buscar_productos_" + index);
            row.find("[id^='grupo_aplicar_descuento']").attr("id", "grupo_aplicar_descuento_" + index);
        });

        $("#checkAll").prop("checked", false);
    }

    function rowIsEmpty(row) {
        var productoID = $.trim(row.find("[name='productoID[]']").val());
        var productName = $.trim(row.find("[name='productName[]']").val());
        var quantity = $.trim(row.find("[name='quantity[]']").val());
        var price = $.trim(row.find("[name='price[]']").val());
        var total = $.trim(row.find("[name='total[]']").val());

        return productoID === "" &&
            productName === "" &&
            quantity === "" &&
            price === "" &&
            total === "";
    }

    function removeEmptyFacturaRows() {
        var rows = $("#formulario_facturacion #invoiceItem tbody tr");

        rows.each(function () {
            var row = $(this);

            if (rowIsEmpty(row) && rows.length > 1) {
                row.remove();
            }
        });

        if ($("#formulario_facturacion #invoiceItem tbody tr").length === 0) {
            limpiarTabla();
        }

        reindexFacturaRows();
    }

    function validateFacturaBeforeSubmit() {
        removeEmptyFacturaRows();
        calculateTotal();

        var pacientes_id = $.trim($("#formulario_facturacion #pacientes_id").val());
        var colaborador_id = $.trim($("#formulario_facturacion #colaborador_id").val());
        var servicio_id = $.trim($("#formulario_facturacion #servicio_id").val());

        if (pacientes_id === "") {
            showFacturaError("Debe seleccionar un paciente antes de guardar la factura.");
            return false;
        }

        if (colaborador_id === "") {
            showFacturaError("Debe seleccionar un profesional antes de guardar la factura.");
            return false;
        }

        if (servicio_id === "") {
            showFacturaError("Debe seleccionar un servicio antes de guardar la factura.");
            return false;
        }

        var hasValidRows = false;
        var productoDuplicado = {};
        var errorMessage = "";

        $("#formulario_facturacion #invoiceItem tbody tr").each(function (index) {
            if (errorMessage !== "") {
                return false;
            }

            var row = $(this);

            var productoID = $.trim(row.find("[name='productoID[]']").val());
            var productName = $.trim(row.find("[name='productName[]']").val());
            var quantity = toNumber(row.find("[name='quantity[]']").val());
            var price = toNumber(row.find("[name='price[]']").val());
            var discount = toNumber(row.find("[name='discount[]']").val());

            if (rowIsEmpty(row)) {
                return true;
            }

            if (productoID === "" || productoID === "0") {
                errorMessage = "La fila " + (index + 1) + " no tiene un producto válido seleccionado.";
                return false;
            }

            if (productName === "") {
                errorMessage = "La fila " + (index + 1) + " no tiene nombre de producto.";
                return false;
            }

            if (quantity <= 0) {
                errorMessage = "La cantidad de la fila " + (index + 1) + " debe ser mayor que cero.";
                return false;
            }

            if (price < 0) {
                errorMessage = "El precio de la fila " + (index + 1) + " no puede ser negativo.";
                return false;
            }

            if (discount < 0) {
                errorMessage = "El descuento de la fila " + (index + 1) + " no puede ser negativo.";
                return false;
            }

            if (discount > (price * quantity)) {
                errorMessage = "El descuento de la fila " + (index + 1) + " no puede ser mayor al total del producto.";
                return false;
            }

            if (productoDuplicado[productoID]) {
                errorMessage = "El producto de la fila " + (index + 1) + " está duplicado. Use una sola fila por producto.";
                return false;
            }

            productoDuplicado[productoID] = true;
            hasValidRows = true;
        });

        if (errorMessage !== "") {
            showFacturaError(errorMessage);
            return false;
        }

        if (!hasValidRows) {
            showFacturaError("El detalle de la factura no puede quedar vacío. Debe agregar al menos un producto o servicio.");
            return false;
        }

        return true;
    }

    window.llenarTablaFactura = function (count) {
        $("#formulario_facturacion #invoiceItem tbody").append(facturaRowHtml(count));
        $("#formulario_facturacion .tableFixHead").scrollTop($(document).height());
        $("#formulario_facturacion #invoiceItem #productName_" + count).focus();
    };

    window.limpiarTabla = function () {
        $("#formulario_facturacion #invoiceItem > tbody").empty();
        $("#formulario_facturacion #invoiceItem > tbody").append(facturaRowHtml(0));

        $("#formulario_facturacion #subTotal").val("");
        $("#formulario_facturacion #taxAmount").val("");
        $("#formulario_facturacion #taxDescuento").val("");
        $("#formulario_facturacion #totalAftertax").val("");
        $("#formulario_facturacion #amountPaid").val("");
        $("#formulario_facturacion #amountDue").val("");

        cleanFooterValueBill();
        reindexFacturaRows();

        $("#formulario_facturacion .tableFixHead").scrollTop($(document).height());
        $("#formulario_facturacion #invoiceItem #productName_0").focus();
    };

    window.addRow = function () {
        var count = getNextFacturaIndex();

        $("#formulario_facturacion #invoiceItem tbody").append(facturaRowHtml(count));
        $("#formulario_facturacion #invoiceItem #productName_" + count).focus();
    };

    window.deleteFacturasDetalles = function (facturas_detalle_id) {
        facturas_detalle_id = $.trim(facturas_detalle_id);

        if (facturas_detalle_id === "" || facturas_detalle_id === "0") {
            return;
        }

        var url = "<?php echo SERVERURL; ?>php/facturacion/deleteFacturasDetalles.php";

        $.ajax({
            type: "POST",
            url: url,
            async: true,
            data: {
                facturas_detalle_id: facturas_detalle_id
            }
        });
    };

    window.calculateTotal = function () {
        var totalAmount = 0;
        var totalDiscount = 0;
        var totalISV = 0;

        $("#formulario_facturacion #invoiceItem tbody tr").each(function () {
            var row = $(this);

            var price = toNumber(row.find("[name='price[]']").val());
            var quantity = toNumber(row.find("[name='quantity[]']").val());
            var discount = toNumber(row.find("[name='discount[]']").val());
            var isv_calculo = toNumber(row.find("[name='valor_isv[]']").val());

            if (rowIsEmpty(row)) {
                row.find("[name='total[]']").val("");
                return true;
            }

            var total = price * quantity;

            row.find("[name='total[]']").val(parseFloat(total).toFixed(2));

            totalAmount += total;
            totalISV += isv_calculo;
            totalDiscount += discount;
        });

        $("#subTotal").val(parseFloat(totalAmount).toFixed(2));
        $("#subTotalFooter").val(parseFloat(totalAmount).toFixed(2));

        $("#taxDescuento").val(parseFloat(totalDiscount).toFixed(2));
        $("#taxDescuentoFooter").val(parseFloat(totalDiscount).toFixed(2));

        $("#taxAmount").val(parseFloat(totalISV).toFixed(2));
        $("#taxAmountFooter").val(parseFloat(totalISV).toFixed(2));

        var totalAftertax = (parseFloat(totalAmount) + parseFloat(totalISV)) - parseFloat(totalDiscount);

        $("#totalAftertax").val(parseFloat(totalAftertax).toFixed(2));
        $("#totalAftertaxFooter").val(parseFloat(totalAftertax).toFixed(2));

        var amountPaid = toNumber($("#amountPaid").val());

        if (amountPaid > 0) {
            $("#amountDue").val(parseFloat(totalAftertax - amountPaid).toFixed(2));
        } else {
            $("#amountDue").val(parseFloat(totalAftertax).toFixed(2));
        }
    };

    window.cleanFooterValueBill = function () {
        $("#subTotalFooter").val("");
        $("#taxAmountFooter").val("");
        $("#totalAftertaxFooter").val("");
    };

    window.getPacientesFacturacion = function () {
        var url = "<?php echo SERVERURL; ?>php/facturacion/getPacientes.php";

        $.ajax({
            type: "POST",
            url: url,
            async: true,
            success: function (data) {
                $("#formulario_facturacion #pacientes_id").html(data);
                $("#formulario_facturacion #pacientes_id").selectpicker("refresh");
            }
        });
    };

    window.getColaboradoresFacturacion = function () {
        var url = "<?php echo SERVERURL; ?>php/facturacion/getColaborador.php";

        $.ajax({
            type: "POST",
            url: url,
            async: true,
            success: function (data) {
                $("#formulario_facturacion #colaborador_id").html(data);
                $("#formulario_facturacion #colaborador_id").selectpicker("refresh");
            }
        });
    };

    window.getServiciosFacturacion = function () {
        var url = "<?php echo SERVERURL; ?>php/citas/getServicioFacturas.php";

        $.ajax({
            type: "POST",
            url: url,
            async: true,
            success: function (data) {
                $("#formulario_facturacion #servicio_id").html(data);
                $("#formulario_facturacion #servicio_id").selectpicker("refresh");
            }
        });
    };

    $(document).ready(function () {
        var formulario = document.getElementById("formulario_facturacion");

        if (formulario) {
            formulario.addEventListener("submit", function (e) {
                if (!validateFacturaBeforeSubmit()) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
            }, true);
        }

        $(document).on("click", "#checkAll", function () {
            $(".itemRow").prop("checked", this.checked);
        });

        $(document).on("click", ".itemRow", function () {
            $("#checkAll").prop(
                "checked",
                $(".itemRow:checked").length === $(".itemRow").length
            );
        });

        $(document).on("click", "#addRows", function () {
            if ($.trim($("#formulario_facturacion #pacientes_id").val()) !== "") {
                addRow();
            } else {
                showFacturaError("Debe seleccionar un paciente antes de agregar productos o servicios.");
            }
        });

        $(document).on("click", "#removeRows", function () {
            if ($(".itemRow").is(":checked")) {
                $(".itemRow:checked").each(function () {
                    var row = $(this).closest("tr");
                    var facturas_detalle_id = $.trim(row.find("[name='facturas_detalle_id[]']").val());

                    deleteFacturasDetalles(facturas_detalle_id);

                    if ($("#formulario_facturacion #invoiceItem tbody tr").length > 1) {
                        row.remove();
                    } else {
                        row.find("input").val("");
                    }
                });

                reindexFacturaRows();
                calculateTotal();
            } else {
                showFacturaError("Debe seleccionar una fila antes de intentar eliminarla.");
            }
        });

        $(document).on("blur keyup change", "[id^='quantity_'], [id^='price_'], [id^='discount_']", function () {
            calculateTotal();
        });

        $(document).on("blur keyup change", "#amountPaid", function () {
            calculateTotal();
        });

        $("#formulario_facturacion #invoiceItem").on("click", ".aplicar_descuento", function (e) {
            e.preventDefault();

            $("#formDescuentoFacturacion")[0].reset();

            var row = $(this).closest("tr");
            var row_index = row.index();
            var col_index = $(this).closest("td").index();

            var paciente = $.trim($("#formulario_facturacion #pacientes_id").val());
            var productoID = $.trim(row.find("[name='productoID[]']").val());

            if (paciente !== "" && productoID !== "") {
                $("#formDescuentoFacturacion #row_index").val(row_index);
                $("#formDescuentoFacturacion #col_index").val(col_index);

                var producto = row.find("[name='productName[]']").val();
                var precio = row.find("[name='price[]']").val();

                $("#formDescuentoFacturacion #descuento_productos_id").val(productoID);
                $("#formDescuentoFacturacion #producto_descuento_fact").val(producto);
                $("#formDescuentoFacturacion #precio_descuento_fact").val(precio);
                $("#formDescuentoFacturacion #pro_descuento_fact").val("Aplicar Descuento");

                $("#modalDescuentoFacturacion").modal({
                    show: true,
                    keyboard: false,
                    backdrop: "static"
                });
            } else {
                showFacturaError("Debe seleccionar un paciente y un producto antes de aplicar descuento.");
            }
        });

        $("#formDescuentoFacturacion #porcentaje_descuento_fact").on("keyup change", function () {
            var precio = toNumber($("#formDescuentoFacturacion #precio_descuento_fact").val());
            var porcentaje = toNumber($(this).val());

            if (porcentaje > 0) {
                $("#formDescuentoFacturacion #descuento_fact").val(parseFloat(precio * (porcentaje / 100)).toFixed(2));
            } else {
                $("#formDescuentoFacturacion #descuento_fact").val("0.00");
            }
        });

        $("#formDescuentoFacturacion #descuento_fact").on("keyup change", function () {
            var precio = toNumber($("#formDescuentoFacturacion #precio_descuento_fact").val());
            var descuento_fact = toNumber($(this).val());

            if (precio > 0 && descuento_fact > 0) {
                $("#formDescuentoFacturacion #porcentaje_descuento_fact").val(parseFloat((descuento_fact / precio) * 100).toFixed(2));
            } else {
                $("#formDescuentoFacturacion #porcentaje_descuento_fact").val("0.00");
            }
        });

        $("#reg_DescuentoFacturacion").on("click", function (e) {
            e.preventDefault();

            var row_index = parseInt($("#formDescuentoFacturacion #row_index").val(), 10);
            var row = $("#formulario_facturacion #invoiceItem tbody tr").eq(row_index);

            if (row.length === 0) {
                showFacturaError("No se encontró la fila para aplicar el descuento.");
                return;
            }

            var descuento = toNumber($("#formDescuentoFacturacion #descuento_fact").val());
            var precio = toNumber(row.find("[name='price[]']").val());
            var cantidad = toNumber(row.find("[name='quantity[]']").val());
            var impuesto_venta = $.trim(row.find("[name='isv[]']").val());

            var total_ = (precio * cantidad) - descuento;

            if (total_ < 0) {
                swal({
                    title: "Advertencia",
                    text: "El valor del descuento es mayor al precio total del artículo, por favor corregir.",
                    icon: "warning",
                    dangerMode: true,
                    closeOnEsc: false,
                    closeOnClickOutside: false
                });
                return;
            }

            row.find("[name='discount[]']").val(parseFloat(descuento).toFixed(2));

            if (impuesto_venta === "1") {
                var porcentaje_isv = parseFloat(getPorcentajeISV("Facturas") / 100);
                var porcentaje_calculo = parseFloat(total_ * porcentaje_isv).toFixed(2);

                row.find("[name='valor_isv[]']").val(porcentaje_calculo);
            } else {
                row.find("[name='valor_isv[]']").val("0.00");
            }

            $("#modalDescuentoFacturacion").modal("hide");
            calculateTotal();
        });
    });
})();
</script>