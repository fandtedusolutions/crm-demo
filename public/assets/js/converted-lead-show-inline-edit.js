(function () {
  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function escapeAttr(str) {
    return escapeHtml(str).replace(/`/g, "&#096;");
  }

  function getCsrfToken() {
    var el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute("content") : "";
  }

  function getCountryCodes() {
    var el = document.getElementById("country-codes-json");
    if (!el) return {};
    try {
      return JSON.parse(el.textContent || "{}");
    } catch (e) {
      return {};
    }
  }

  function buildCountryOptions(selected) {
    var codeOptions = getCountryCodes();
    var opts = '<option value="">Select Country</option>';
    Object.keys(codeOptions).forEach(function (code) {
      var isSel = String(selected) === String(code) ? "selected" : "";
      opts +=
        '<option value="' +
        escapeHtml(code) +
        '" ' +
        isSel +
        ">" +
        escapeHtml(code + " - " + codeOptions[code]) +
        "</option>";
    });
    return opts;
  }

  function createPhoneForm(codeField, currentCode, currentPhone) {
    var safePhone = currentPhone && currentPhone !== "N/A" ? currentPhone : "";
    return (
      '<div class="edit-form-show-cl">' +
      '<div class="row g-1">' +
      '<div class="col-5">' +
      '<select class="form-select form-select-sm" name="' +
      escapeAttr(codeField) +
      '">' +
      buildCountryOptions(currentCode) +
      "</select>" +
      "</div>" +
      '<div class="col-7">' +
      '<input type="text" class="form-control form-control-sm" value="' +
      escapeAttr(safePhone) +
      '" placeholder="Phone number" autocomplete="off" />' +
      "</div>" +
      "</div>" +
      '<div class="btn-group mt-1">' +
      '<button type="button" class="btn btn-success btn-sm save-edit-show-cl">Save</button>' +
      '<button type="button" class="btn btn-secondary btn-sm cancel-edit-show-cl">Cancel</button>' +
      "</div>" +
      "</div>"
    );
  }

  function createBatchForm() {
    return (
      '<div class="edit-form-show-cl">' +
      '<select class="form-select form-select-sm"><option value="">Loading...</option></select>' +
      '<div class="btn-group mt-1">' +
      '<button type="button" class="btn btn-success btn-sm save-edit-show-cl">Save</button>' +
      '<button type="button" class="btn btn-secondary btn-sm cancel-edit-show-cl">Cancel</button>' +
      "</div>" +
      "</div>"
    );
  }

  function getSelectOptions(container, field) {
    if (field === "second_language") {
      return { "": "Select Language", malayalam: "Malayalam", hindi: "Hindi" };
    }

    var raw = container.attr("data-options-json");
    if (!raw) return {};

    try {
      return JSON.parse(raw);
    } catch (e) {
      return {};
    }
  }

  function createSelectForm(container, field, current) {
    var options = getSelectOptions(container, field);
    var normalizedCurrent = current;

    if (field === "second_language" && normalizedCurrent) {
      normalizedCurrent = String(normalizedCurrent).toLowerCase();
    }

    var opts = "";
    Object.keys(options).forEach(function (key) {
      var selected =
        String(key) === String(normalizedCurrent) ? "selected" : "";
      opts +=
        '<option value="' +
        escapeHtml(String(key)) +
        '" ' +
        selected +
        ">" +
        escapeHtml(String(options[key])) +
        "</option>";
    });

    return (
      '<div class="edit-form-show-cl">' +
      '<select class="form-select form-select-sm">' +
      opts +
      "</select>" +
      '<div class="btn-group mt-1">' +
      '<button type="button" class="btn btn-success btn-sm save-edit-show-cl">Save</button>' +
      '<button type="button" class="btn btn-secondary btn-sm cancel-edit-show-cl">Cancel</button>' +
      "</div>" +
      "</div>"
    );
  }

  function createTextForm(type, current) {
    var safe = current === "N/A" ? "" : current;
    var inputHtml;

    if (type === "date") {
      inputHtml =
        '<input type="date" class="form-control form-control-sm" value="' +
        escapeAttr(safe) +
        '" />';
    } else if (type === "textarea") {
      inputHtml =
        '<textarea class="form-control form-control-sm" rows="3">' +
        escapeHtml(safe) +
        "</textarea>";
    } else if (type === "email") {
      inputHtml =
        '<input type="email" class="form-control form-control-sm" value="' +
        escapeAttr(safe) +
        '" autocomplete="off" />';
    } else {
      inputHtml =
        '<input type="text" class="form-control form-control-sm" value="' +
        escapeAttr(safe) +
        '" autocomplete="off" />';
    }

    return (
      '<div class="edit-form-show-cl">' +
      inputHtml +
      '<div class="btn-group mt-1">' +
      '<button type="button" class="btn btn-success btn-sm save-edit-show-cl">Save</button>' +
      '<button type="button" class="btn btn-secondary btn-sm cancel-edit-show-cl">Cancel</button>' +
      "</div>" +
      "</div>"
    );
  }

  function loadBatches(courseId, select, currentId) {
    if (!courseId) {
      select.html('<option value="">No course selected</option>');
      return;
    }

    $.get("/api/batches/by-course/" + courseId)
      .done(function (response) {
        var options = '<option value="">Select Batch</option>';
        if (response.success && response.batches) {
          response.batches.forEach(function (batch) {
            var isSelected =
              currentId && String(currentId) === String(batch.id)
                ? "selected"
                : "";
            options +=
              '<option value="' +
              batch.id +
              '" ' +
              isSelected +
              ">" +
              escapeHtml(batch.title) +
              "</option>";
          });
        }
        select.html(options);
        select.focus();
      })
      .fail(function () {
        select.html('<option value="">Error loading batches</option>');
      });
  }

  function init() {
    if (typeof window.jQuery === "undefined") return;
    var $ = window.jQuery;

    var $config = $("#jsConvertedLeadShowConfig");
    if ($config.length === 0) return;

    var inlineUrl = $config.attr("data-inline-url");

    $(document)
      .off("click.clShowEdit", ".edit-btn-show-cl")
      .on("click.clShowEdit", ".edit-btn-show-cl", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var container = $(this).closest(".inline-edit-show-cl");
        if (container.hasClass("editing")) return;

        $(".inline-edit-show-cl.editing")
          .not(container)
          .each(function () {
            $(this).removeClass("editing");
            $(this).find(".edit-form-show-cl").remove();
          });

        var field = container.attr("data-field");
        var type = container.attr("data-type") || "text";
        var current = container.attr("data-current") || "";
        var html = "";

        if (type === "phone") {
          var codeField = container.attr("data-code-field") || "code";
          var currentCode = container.attr("data-current-code") || "";
          html = createPhoneForm(codeField, currentCode, current);
        } else if (type === "select" && field !== "lead_detail_batch_id") {
          html = createSelectForm(container, field, current);
        } else if (field === "lead_detail_batch_id") {
          html = createBatchForm();
        } else {
          html = createTextForm(type, current);
        }

        container.addClass("editing");
        container.append(html);

        if (field === "lead_detail_batch_id") {
          var courseId = container.attr("data-course-id");
          var currentId = container.attr("data-current-id") || "";
          loadBatches(courseId, container.find("select"), currentId);
        } else {
          container.find("input, select, textarea").first().focus();
        }
      });

    $(document)
      .off("click.clShowCancel", ".cancel-edit-show-cl")
      .on("click.clShowCancel", ".cancel-edit-show-cl", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var container = $(this).closest(".inline-edit-show-cl");
        container.removeClass("editing");
        container.find(".edit-form-show-cl").remove();
      });

    $(document)
      .off("click.clShowSave", ".save-edit-show-cl")
      .on("click.clShowSave", ".save-edit-show-cl", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var container = $(this).closest(".inline-edit-show-cl");
        var field = container.attr("data-field");
        var type = container.attr("data-type") || "text";
        var payload = {
          field: field,
          _token: getCsrfToken(),
        };

        if (type === "phone") {
          var codeField = container.attr("data-code-field") || "code";
          payload.value = container.find('input[type="text"]').val();
          payload[codeField] = container.find("select").val();
        } else {
          payload.value = container.find("input, select, textarea").val();
        }

        var btn = $(this);
        if (btn.data("busy")) return;
        btn.data("busy", true);
        btn.prop("disabled", true).html('<i class="ti ti-loader-2 spin"></i>');

        $.ajax({
          url: inlineUrl,
          method: "POST",
          data: payload,
          success: function (res) {
            if (res && res.success) {
              var displayValue = res.value || payload.value || "N/A";
              container.find(".display-value").text(displayValue);

              if (type === "phone") {
                container.attr("data-current", payload.value || "");
                container.attr(
                  "data-current-code",
                  payload[container.attr("data-code-field") || "code"] || ""
                );
              } else if (field === "lead_detail_batch_id") {
                container.attr("data-current-id", payload.value || "");
                container.attr("data-current", payload.value || "");
              } else if (type === "date") {
                container.attr("data-current", payload.value || "");
              } else {
                container.attr("data-current", payload.value || "");
              }

              if (field === "name") {
                var initial = displayValue && String(displayValue).trim().length
                  ? String(displayValue).trim().charAt(0).toUpperCase()
                  : "?";
                $(".js-cl-show-name-initial").text(initial);
                $(".js-cl-show-name-heading").text(displayValue);
              }

              var successMsg = res.message || "Updated successfully";
              if (typeof window.toast_success === "function") {
                window.toast_success(successMsg);
              } else if (typeof window.showToast === "function") {
                window.showToast(successMsg, "success");
              }
            } else {
              var msg =
                res && (res.error || res.message)
                  ? res.error || res.message
                  : "Update failed";
              if (typeof window.toast_error === "function") {
                window.toast_error(msg);
              } else if (typeof window.showToast === "function") {
                window.showToast(msg, "error");
              }
            }
          },
          error: function (xhr) {
            var msg = "Update failed";
            if (xhr && xhr.responseJSON && xhr.responseJSON.error) {
              msg = xhr.responseJSON.error;
            }
            if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
              try {
                msg = Object.values(xhr.responseJSON.errors).flat().join(", ");
              } catch (err) {}
            }
            if (typeof window.toast_error === "function") {
              window.toast_error(msg);
            } else if (typeof window.showToast === "function") {
              window.showToast(msg, "error");
            }
          },
          complete: function () {
            btn.data("busy", false);
            btn.prop("disabled", false).html("Save");
            container.removeClass("editing");
            container.find(".edit-form-show-cl").remove();
          },
        });
      });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
