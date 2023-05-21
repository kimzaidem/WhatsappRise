let wa_tribute;
$(function () {
    "use strict";
    $(document.body).on("change", "#category", function (event) {

        if (["client", "proposals"].indexOf($(this).val()) !== -1) {
            $('#send_to').find("option[value='staff']").attr('disabled', true);
        }
        if (["leads"].indexOf($(this).val()) !== -1) {
            $('#send_to').find("option[value='contact']").attr('disabled', true);
        }

        var selectedValue = $(this).val();
        var fields = _.filter(merge_fields, function (num) {
            return (
                    typeof num[selectedValue] != "undefined" ||
                    typeof num["other"] != "undefined" ||
                    typeof num["staff"] != "undefined" ||
                    typeof num["client"] != "undefined"
                    );
        });

        var other_index = _.findIndex(fields, function (data) {
            return _.allKeys(data)[0] == "other";
        });
        var staff_index = _.findIndex(fields, function (data) {
            return _.allKeys(data)[0] == "staff";
        });
        var client_index = _.findIndex(fields, function (data) {
            return _.allKeys(data)[0] == "client";
        });
        var selected_index = _.findIndex(fields, function (data) {
            return _.allKeys(data)[0] == selectedValue;
        });

        var options = [];

        if (fields[selected_index]) {
            fields[selected_index][selectedValue].forEach((field) => {
                if (field.name != "") {
                    options.push({ key: field.name, value: field.key });
                }
            });
        }
        if (fields[other_index]) {
            fields[other_index]["other"].forEach((field) => {
                if (field.name != "") {
                    options.push({ key: field.name, value: field.key });
                }
            });
        }
        if (fields[staff_index] && ["client", "proposals"].indexOf($(this).val()) === -1) {
            fields[staff_index]["staff"].forEach((field) => {
                if (field.name != "") {
                    options.push({ key: field.name, value: field.key });
                }
            });
        }
        if (fields[client_index] && ["client", "leads"].indexOf($(this).val()) === -1) {
            fields[client_index]["client"].forEach((field) => {
                if (field.name != "") {
                    options.push({ key: field.name, value: field.key });
                }
            });
        }

        wa_tribute = new Tribute({
            values: options,
            selectClass: "highlights",
        });
        wa_tribute.detach(document.querySelectorAll(".mentionable"));
        wa_tribute.attach(document.querySelectorAll(".mentionable"));
    });
    $("#category").trigger("change");
});

function refreshTribute() {
  "use strict";
  if ($("#category").val() != "") {
    wa_tribute.attach(document.querySelectorAll(".mentionable"));
  }
}
