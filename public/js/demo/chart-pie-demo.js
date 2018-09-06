// Set new default font family and font color to mimic Bootstrap's default styling
(function ($,Window,document,Chart) {
    Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#292b2c';
    const RACINE="/DarmoticsApp/public";
// Pie Chart Example

    $.ajax({
        url: RACINE+"/Home/chartData",
        method: "POST"

        })
        .done(function (data) {
            console.log(data);
            var toUse = JSON.parse(data);
            var treasuryData = toUse.treasuryData;
            var capitalData = toUse.capitalData;

            /*drawing of treasury chart*/
            var treasury = document.getElementById("treasuryChart");
            var treasuryChart = new Chart(treasury, {
                type: 'pie',
                data: {
                    labels: Object.keys(treasuryData),
                    datasets: [{
                        data: Object.values(treasuryData),
                        backgroundColor: ['#007bff', '#dc3545','#ffc107', '#28a745','#f8c74a', '#fc3545','#307bfe', '#6b3545', '#7b3545', '#6a354e'],
                    }],
                },
            })

            /*drawing of capital chart*/
            /* colors = {};
            for (let i =0,m=Object.keys(capitalData).length; i<m;i++){
              colors[i.toString()] = '#0'+i+'fe'+i+'b';
            }
            console.log(colors);*/
            var capital = document.getElementById("capitalChart");
            var capitalChart = new Chart(capital, {
                type: 'pie',
                data: {
                    labels: Object.keys(capitalData),
                    datasets: [{
                        data: Object.values(capitalData),
                        backgroundColor: ['#007bff', '#dc3545','#ffc107', '#28a745','#f8c74a', '#fc3545','#307bfe', '#6b3545', '#7b3545', '#6a354e'],
                    }],
                },
            });

        })
        .fail(function(data){
      console.log("echec de la collection des données");
    })




})($,window,document,Chart);
