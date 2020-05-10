var mesAtu = moment().format('MM');
var diaAtu = moment().format('DD');

$(document).ready(function () {
  $.fn.dataTable.moment('DD/MM');

  jsLIB.ajaxCall({
    waiting: true,
    type: "GET",
    url: jsLIB.rootDir + "rules/painelAprendizadoUnidade.php",
    data: { MethodName: 'getGraphData' },
    success: function (data) {
      if (data.cls) {
        var optionBar = {
          series: {
            stack: false,
            shadowSize: false,
            bars: {
              show: true,
              barWidth: 0.7,
              align: "center",
              zero: true,
              horizontal: false,
              fillColor: {
                colors: [
                  { opacity: 0.9 },
                  { opacity: 0.75 }
                ]
              },
              fill: true,
              lineWidth: 2
            }
          },
          legend: {
            show: true,
            labelFormatter: function (v, axis) {
              return "&nbsp;" + v + "%&nbsp;";
            },
            noColumns: 12,
            backgroundOpacity: 1
          },
          xaxis: {
            show: true,
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 10,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 10,
            tickFormatter: function (v, axis) {
              return "";
            },
            ticks: data.ticks
          },
          yaxis: {
            show: true,
            axisLabel: "ITENS ASSINADOS",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 10,
            axisLabelFontFamily: 'Verdana, Arial',
            min: 0,
            max: 100,
            tickFormatter: function (v, axis) {
              return v + "%";
            }
          }
        };
        $("[name=graphicC]").show();
        $.plot("#phGhaphC", data.cls, optionBar);
      }
    }
  });

  $('.panel')
    .on('click', function (e) {
    })
    .on('show.bs.collapse', function (e) {
      var idCad = $(this).attr("cad-id");
      showDetailClass(this, { id: idCad, un: $(this).attr("unidade") });
    })
    .on('hide.bs.collapse', function (e) {
      $(this).find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
    });

  $('#members').DataTable({
    lengthChange: false,
    ordering: true,
    paging: false,
    scrollY: 295,
    searching: false,
    processing: true,
    language: {
      info: "_END_ membros",
      search: "",
      searchPlaceholder: "Procurar...",
      infoFiltered: " de _MAX_",
      loadingRecords: "Aguarde - carregando...",
      zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
      infoEmpty: "0 encontrados"
    },
    ajax: {
      type: "GET",
      url: jsLIB.rootDir + "rules/painelAprendizadoUnidade.php",
      data: function (d) {
        d.MethodName = "getMembros",
          d.data = {
            filtro: 'A',
            filters: jsFilter.jSON()
          }
      },
      dataSrc: "membros"
    },
    columns: [
      {
        data: "dm",
        visible: false
      },
      {
        data: "nm",
        type: 'ptbr-string',
        width: "46%",
        sortable: true
      },
      {
        data: "cgo",
        width: "20%",
        sortable: true
      },
      {
        data: "pho",
        width: "20%",
        sortable: false
      },
      {
        data: "dm",
        width: "7%",
        sortable: true,
        render: function (data) {
          return moment.unix(data).format("DD/MM")
        }
      },
      {
        data: "ih",
        width: "7%",
        sortable: true
      }
    ],
    fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      var mesAn = moment.unix(aData.dm).format('MM');
      if (mesAn < mesAtu) {
        $('td', nRow).css('color', '#d0d0d0');
      } else if (mesAn == mesAtu) {
        if (diaAtu == moment.unix(aData.dm).format('DD')) {
          $('td', nRow).css('background-color', '#FFACAA');
        } else {
          $('td', nRow).css('background-color', '#FFFFAA');
        }
      }
    }
  }).order([0, 'asc'], [1, 'asc']);
});