$(function () {
  // =====================================
  // Propinsi - Preview Card
  // =====================================
    $.ajax({
        url: "/api/provcount",
        type: "GET",
        dataType: 'json',
        success: function (res) {

          let breakup = {
            color: "#adb5bd",
            series: res.map(item => item.record_count),
            labels: res.map(item => item.nm_prov),
            chart: {
              width: 60,
              height: 60,
              type: "donut",
              fontFamily: "Plus Jakarta Sans', sans-serif",
              foreColor: "#adb0bb",
            },
            plotOptions: {
              pie: {
                startAngle: 0,
                endAngle: 360,
                donut: {
                  size: '70%',
                },
              },
            },
            stroke: {
              show: false,
            },

            dataLabels: {
              enabled: false,
            },

            legend: {
              show: false,
            },
            colors: ["#5D87FF", "#49BEFF", "#13DEB9", "#FA896B", "#FFAE1F"],

            tooltip: {
              enabled: false,
            },
          };

          $(".count-prop").text(res.length)
          var chart = new ApexCharts(document.querySelector("#propinsi-preview"), breakup);
          chart.render();
        }
    });

    // =====================================
    // Kabupaten - Preview Card
    // =====================================
    function chartKabupaten(uri) {
        //Reset Chart
        $("#kabupaten-preview").html("");
        //Create Chart
        $.ajax({
            url: uri,
            type: "GET",
            dataType: 'json',
            success: function (res) {

                let breakup = {
                    color: "#adb5bd",
                    series: res.map(item => item.record_count),
                    labels: res.map(item => item.nama_kab),
                    chart: {
                        width: 60,
                        height: 60,
                        type: "donut",
                        fontFamily: "Plus Jakarta Sans', sans-serif",
                        foreColor: "#adb0bb",
                    },
                    plotOptions: {
                        pie: {
                            startAngle: 0,
                            endAngle: 360,
                            donut: {
                                size: '70%',
                            },
                        },
                    },
                    stroke: {
                        show: false,
                    },

                    dataLabels: {
                        enabled: false,
                    },

                    legend: {
                        show: false,
                    },
                    colors: ["#14A4C6", "#49BEFF", "#13DEB9", "#FA896B", "#FFAE1F"],

                    tooltip: {
                        enabled: false,
                    },
                };

                $(".count-kab").text(res.length)
                var chart = new ApexCharts(document.querySelector("#kabupaten-preview"), breakup);
                chart.render();
            }
        });
    }
    if ($("#kabupaten-preview").length > 0) {
        chartKabupaten("/api/kabcount");
    }

    // =====================================
    // Pengurus Cabang - Preview Card
    // =====================================
    function chartPC(uri) {
        //Reset Chart
        $("#pc-preview").html("");
        //Create Chart
        $.ajax({
            url: uri,
            type: "GET",
            dataType: 'json',
            success: function (res) {

                let breakup = {
                    color: "#adb5bd",
                    series: res.map(item => item.record_count),
                    labels: res.map(item => item.nama_pc),
                    chart: {
                        width: 60,
                        height: 60,
                        type: "donut",
                        fontFamily: "Plus Jakarta Sans', sans-serif",
                        foreColor: "#adb0bb",
                    },
                    plotOptions: {
                        pie: {
                            startAngle: 0,
                            endAngle: 360,
                            donut: {
                                size: '70%',
                            },
                        },
                    },
                    stroke: {
                        show: false,
                    },

                    dataLabels: {
                        enabled: false,
                    },

                    legend: {
                        show: false,
                    },
                    colors: ["#14A4C6", "#49BEFF", "#13DEB9", "#FA896B", "#FFAE1F"],

                    tooltip: {
                        enabled: false,
                    },
                };

                $(".count-pc").text(res.length)
                var chart = new ApexCharts(document.querySelector("#pc-preview"), breakup);
                chart.render();
            }
        });
    }

    if ($("#pc-preview").length > 0) {
        chartPC("/api/pccount");
    }

    // =====================================
    // Jenjang Pendidikan - Preview Card
    // =====================================
    $.ajax({
        url: "/api/jenjangcount",
        type: "GET",
        dataType: 'json',
        success: function (res) {

            let breakup = {
                color: "#adb5bd",
                series: res.map(item => item.record_count),
                labels: res.map(item => item.nm_jenjang),
                chart: {
                    width: 60,
                    height: 60,
                    type: "donut",
                    fontFamily: "Plus Jakarta Sans', sans-serif",
                    foreColor: "#adb0bb",
                },
                plotOptions: {
                    pie: {
                        startAngle: 0,
                        endAngle: 360,
                        donut: {
                            size: '70%',
                        },
                    },
                },
                stroke: {
                    show: false,
                },

                dataLabels: {
                    enabled: false,
                },

                legend: {
                    show: false,
                },
                colors: ["#13DEB9", "#49BEFF", "#5D87FF", "#FA896B", "#FFAE1F"],

                tooltip: {
                    enabled: false,
                },
            };

            $(".count-jp").text(res.length)
            var chart = new ApexCharts(document.querySelector("#jenjang-preview"), breakup);
            chart.render();
        }
    });

    $("#chartSelectProv").on('change', function() {
        chartKabupaten("/api/kabcount/" + $(this).val());
    });

})
