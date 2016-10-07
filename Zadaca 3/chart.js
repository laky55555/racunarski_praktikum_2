function pronadji_me(imena, ime){
  for(var i = 0; i < imena.length; ++i)
    if(ime == imena[i])
      return i;
  return -1;
}

function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function crtaj1(imena, vrijednosti, my_title, x_label, y_label, i, max_duljina_osi, imena_mjerenja){

  //canvas
  var ctx = $("#" + i.toString()).get(0).getContext("2d");

  //pronadjimo najdulji naziv proizvoda ili y-ois zbog određivaja x_ishodista
  var max_ime = 0;
  ctx.font="15px Georgia";
  for(var k = 0; k < imena.length; ++k){
    var duljina =  ctx.measureText(imena[k]).width;
    if(max_ime <  duljina)
      max_ime = duljina;
  }
  if(y_label && ctx.measureText(y_label).width > max_ime){
      max_ime = ctx.measureText(y_label).width;
  }

  //polje boje sadrzi boje za tocno onoliko mjerenja koliko imamo
  var boje = [];
  for(var i = 0; i < imena_mjerenja.length; i++)
    boje.push(getRandomColor());

  //skaliranje vrijednosti i definiranje konstanti
  var povecanje_x = (window.innerWidth * 0.5)/max_duljina_osi ;
  var povecanje_y = 1;
  var debljina_prav = 20;
  var udaljenost_prav = 30;
  var max_duljina_x = max_duljina_osi * povecanje_x;
  var max_duljina_y = (vrijednosti.length * debljina_prav + imena.length * udaljenost_prav 
                                      + 20 * imena_mjerenja.length) * povecanje_y;
  var ishodiste_x = max_ime + 10;
  var ishodiste_y = max_duljina_y * 1.3;
  ctx.canvas.width = window.innerWidth*0.7;
  ctx.canvas.height = max_duljina_y * 1.9;


  //crtanje x osi
  ctx.beginPath();
  ctx.moveTo(ishodiste_x, ishodiste_y);
  ctx.lineTo(ishodiste_x + max_duljina_x + 50, ishodiste_y );
  ctx.stroke();

  //crtanje y osi
  ctx.beginPath();
  ctx.moveTo(ishodiste_x, ishodiste_y);
  ctx.lineTo(ishodiste_x, ishodiste_y - max_duljina_y -20 );
  ctx.stroke();

  //crtanje pravokutika u dijagramu
  for(var j = 0; j < vrijednosti.length; ++j){
    var rectangle = new Path2D();
    var x = ishodiste_x;
    var pomak1 = (j % imena.length) * (vrijednosti.length/imena.length);    //kojem naslovu pripada
    var pomak2 = parseInt(j/imena.length) ;
    var y = ishodiste_y - ((pomak1+pomak2)*debljina_prav + (j%imena.length)*udaljenost_prav) 
                                                * povecanje_y - debljina_prav;

    var duljina_x = vrijednosti[j] * povecanje_x;
    var duljina_y = debljina_prav;
    ctx.fillStyle=boje[(parseInt(j/imena.length))%4];
    rectangle.rect(x, y, duljina_x, duljina_y);
    ctx.fill(rectangle);
    ctx.font = "20px Calibri";
    ctx.fillStyle = "white";
    var duljina_broja = ctx.measureText(vrijednosti[j].toString()).width;
    ctx.fillText(vrijednosti[j], x +  duljina_x - duljina_broja-10, y + debljina_prav * 0.8);
  }

  //ispis imena mjerenja
  for(var j = 0; j < imena_mjerenja.length; ++j){
    var rectangle = new Path2D();
    ctx.fillStyle=boje[j];
    rectangle.rect(ishodiste_x, ishodiste_y + 50*(j+1), 30, 30);
    ctx.fill(rectangle);
    ctx.font = "20px Calibri";
    ctx.fillStyle = "blue";
    ctx.fillText(imena_mjerenja[j], ishodiste_x + 50, ishodiste_y + 50 * (j+1) + 30 );
  }

  //pisanje naslova
  ctx.font="30px Georgia";
  ctx.fillStyle="blue";
  ctx.fillText(my_title, ishodiste_x, ishodiste_y - max_duljina_y - 50);
 
  //pisanje imena na y osi
  ctx.font="15px Georgia";
  var mjerenja = vrijednosti.length/imena.length;
  var pomak = debljina_prav * mjerenja + udaljenost_prav;
  var pocetak =ishodiste_y - 0.5 * debljina_prav * mjerenja;
  for(var j = 0; j < imena.length; ++j){
      ctx.fillText(imena[j], ishodiste_x - max_ime - 5, pocetak);
      console.log()
      pocetak = pocetak - pomak;
  }

  //pisanje oznaka osi
  if(x_label){
    ctx.fillText(x_label, ishodiste_x + 0.90 * max_duljina_x, ishodiste_y +20);
  }
  if(y_label){
    ctx.fillText(y_label, ishodiste_x - ctx.measureText(y_label).width - 5, ishodiste_y - max_duljina_y - 10)
  }
}

function crtaj2(imena, vrijednosti, my_title, x_label, y_label, i, max_duljina_osi, imena_mjerenja){
  //canvas
  var ctx = $("#" + i.toString()).get(0).getContext("2d");

  var max_ime = 0;
  ctx.font="20px Georgia";
  for(var k = 0; k < imena.length; ++k){
    var duljina =  ctx.measureText(imena[k]).width;
    if(max_ime <  duljina)
      max_ime = duljina;
  }
  //prosirivanje grafa ispod x osi
  max_ime =  max_ime + 50 * imena_mjerenja.length; 
  
  //debljina pravokutnika ovisi o tome koliko imamo mjerenja, i moraju svi stati u velicinu ekran
  var boje = [];
   for(var i = 0; i < imena_mjerenja.length; i++)
    boje.push(getRandomColor());
  ctx.canvas.width = 1600;
  ctx.font="20px Georgia";
  ctx.fillStyle="blue";
  var ishodiste_x = ctx.measureText(y_label).width + 20;
  var max_duljina_x = (ctx.canvas.width - ishodiste_x -20) * 0.9;
  var debljina_prav = max_duljina_x/(vrijednosti.length + imena.length);
  console.log("debljina pravokutnika je " + debljina_prav);
  if(debljina_prav < 30){
    debljina_prav = 30;
    ctx.canvas.width = ((vrijednosti.length + imena.length) * debljina_prav + ishodiste_x) * 1.1;
    var max_duljina_x = (ctx.canvas.width - ishodiste_x -20) * 0.9;
  }

  var udaljenost_prav = debljina_prav;
  ctx.canvas.height = 400 + max_ime;
  var povecanje = ((ctx.canvas.height - max_ime) * 0.6)/max_duljina_osi;
  var ishodiste_y = 0.9 * (ctx.canvas.height - max_ime);
  //crtanje x osi
  ctx.beginPath();
  ctx.moveTo(ishodiste_x, ishodiste_y);
  ctx.lineTo(0.9 * ctx.canvas.width, ishodiste_y );
  ctx.stroke();

  //crtanje y osi
  ctx.beginPath();
  ctx.moveTo(ishodiste_x, ishodiste_y);
  ctx.lineTo(ishodiste_x, 0.2 * (ctx.canvas.height - max_ime) );
  ctx.stroke();

  //crtanje pravokutika u dijagramu
  for(var j = 0; j < vrijednosti.length; ++j){
    var rectangle = new Path2D();
    var y = ishodiste_y;
    var pomak1 = (j % imena.length) * (vrijednosti.length/imena.length);    //kojem naslovu pripada
    var pomak2 = parseInt(j/imena.length) ;
    var x = ishodiste_x + ((pomak1+pomak2)*debljina_prav + (j%imena.length)*udaljenost_prav);

    var duljina_y = vrijednosti[j] * povecanje;
    var duljina_x = debljina_prav;
    ctx.fillStyle=boje[(parseInt(j/imena.length))%4];
    rectangle.rect(x, y, duljina_x, -duljina_y);
    ctx.fill(rectangle);
    ctx.font = "20px Calibri";
    ctx.fillStyle = "white";
    var duljina_broja = ctx.measureText(vrijednosti[j].toString()).width;
    ctx.save();
    ctx.translate(x + debljina_prav * 0.3, y - duljina_y + 5);
    ctx.rotate(Math.PI/2);
    ctx.textAlign="left";
    ctx.fillText(vrijednosti[j], 0 , 0);
    ctx.restore();
  }

  //crtanje imena_mjerenja
  for(var j = 0; j < imena_mjerenja.length; ++j){
    var rectangle = new Path2D();
    ctx.fillStyle=boje[j];
    rectangle.rect(ishodiste_x, ishodiste_y + 50*(j+1), 30, 30);
    ctx.fill(rectangle);
    ctx.font = "20px Calibri";
    ctx.fillStyle = "blue";
    ctx.fillText(imena_mjerenja[j], ishodiste_x + 70, ishodiste_y + 50 * (j+1) + 30 );
  }

  //pisanje oznaka osi
  ctx.font="20px Georgia";
  ctx.fillStyle="blue";
  if(x_label){
    ctx.fillText(x_label, 0.9 * ctx.canvas.width - ctx.measureText(x_label).width, ishodiste_y +20);
  }
  if(y_label){
    ctx.fillText(y_label, ishodiste_x - ctx.measureText(y_label).width - 5, ishodiste_y - 0.7 * (ctx.canvas.height - max_ime));
  }

  //pisanje imena
  ctx.font="20px Georgia";
  var mjerenja = vrijednosti.length/imena.length;
  var pomak = debljina_prav * mjerenja + udaljenost_prav;
  var pocetak =ishodiste_x + 0.5 * debljina_prav * mjerenja;
  for(var j = 0; j < imena.length; ++j){
    ctx.save();
    ctx.translate(pocetak, ishodiste_y + 20);
    ctx.rotate(Math.PI/8);
    ctx.textAlign="left";
    ctx.fillText(imena[j], 0 , 0);
    ctx.restore();
    pocetak = pocetak + pomak;
  }

  //pisanje naslova
  ctx.font="30px Georgia";
  ctx.fillStyle="blue";
  ctx.fillText(my_title, ishodiste_x, ishodiste_y - 0.8 * (ctx.canvas.height - max_ime));
}

function crtaj3(imena, vrijednosti, my_title, x_label, y_label, i, max_duljina_osi, imena_mjerenja){
  var ctx = $("#" + i.toString()).get(0).getContext("2d");

  ctx.canvas.width = 1600;
  ctx.canvas.height = 50 * (vrijednosti.length/imena.length) + 60 * imena.length + 100;

  var boje = [];
  for(var i = 0; i < imena.length; i++)
    boje.push(getRandomColor());

  var max_ime_mjerenja = 0;
  ctx.font = "25px Georgia";
  for(var k = 0; k < imena_mjerenja.length; ++k){
    var duljina =  ctx.measureText(imena_mjerenja[k]).width;
    if(max_ime_mjerenja <  duljina)
      max_ime_mjerenja = duljina;
  }
  
  var zbroj;
  var postotak = [];
  var y = 50;
  ctx.font = "30px Georgia";
  ctx.fillStyle = "blue";
  ctx.fillText(my_title, 40, y );
  y += 60;

  //zbrojit sve vrijednosti pojedinog mjerenja i izracunat 
  //udio pojedinog mjerenja
  for(var j = 0; j < vrijednosti.length/imena.length; ++j){
      zbroj = 0;
      var x = max_ime_mjerenja + 20;
      for(var k = j*imena.length; k < (j+1)*imena.length; ++k){
        zbroj += vrijednosti[k];
      }
      ctx.font = "25px Georgia";
      ctx.fillStyle = "blue";
      ctx.fillText(imena_mjerenja[j], 10, y+30 );

      console.log("zbroj je " + zbroj + " od mjerenja " + j);
      for(var k = j*imena.length; k < (j+1)*imena.length; ++k){
        postotak[k] = (vrijednosti[k]/zbroj)*1400;
        console.log(postotak[k]);
        var rectangle = new Path2D();
        ctx.fillStyle=boje[k%imena.length];
        rectangle.rect(x, y, postotak[k], 30);
        ctx.fill(rectangle);
        x += postotak[k];
      }
      y += 40;
  }
  y += 40;
  for(var j = 0; j < imena.length; ++j){
    x = 50;
    var rectangle = new Path2D();
    ctx.fillStyle=boje[j];
    rectangle.rect(x, y, 30, -30);
    ctx.fill(rectangle);
    ctx.font = "25px Georgia";
    ctx.fillStyle = "blue";
    ctx.fillText(imena[j], x + 40, y );
    y += 40;
  }
}

window.onload = function(){

  function Graf(imena, vrijednosti, my_title, x_label, y_label, i, max_duljina_osi, imena_mjerenja){
    this.imena = imena;
    this.vrijednosti = vrijednosti; 
    this.my_title = my_title;
    this.x_label = x_label;
    this.y_label = y_label;
    this.i = i;
    this.max_duljina_osi = max_duljina_osi;
    this.imena_mjerenja = imena_mjerenja;
  }

  //dohvatimo podatke
  var charts = $(".chart");
  var grafovi = [];
  for( var i = 0; i < charts.length; ++i ){
    charts.eq(i).prop("id", i.toString());
    
    var my_title = charts.eq(i).prop('title');
    var x_label = charts.eq(i).attr('data-xlabel');
    var y_label = charts.eq(i).attr('data-ylabel');
    if(!x_label)
      x_label='';
    if(!y_label)
      y_label='';

    var potencijalni_podaci = charts.eq(i).children();
    
    var imena = [];
    var vrijednosti = [];
    var max_duljina_osi = 0;
    var imena_mjerenja = [];
    charts.eq(i).replaceWith('<canvas id="' + i.toString() + '">');
    for( var k = 0; k < potencijalni_podaci.length; ++k ){
      if(potencijalni_podaci.eq(k).attr('data-dataset')){
        imena_mjerenja.push(potencijalni_podaci.eq(k).attr('data-dataset'));
        var spans = potencijalni_podaci.eq(k).children();
        for(var j = 0; j < spans.length; ++j){
          if( k == 0 ){
            imena.push(spans.eq(j).html());
            vrijednosti.push(parseFloat(spans.eq(j).attr("data-value")));
          }
          else {
            var gdje = pronadji_me(imena, spans.eq(j).html());
            vrijednosti[gdje + imena.length * k] = parseFloat(spans.eq(j).attr("data-value"));
          }
          if(max_duljina_osi < parseFloat(spans.eq(j).attr("data-value")))
            max_duljina_osi = parseFloat(spans.eq(j).attr("data-value"));
        }
      }
    }
    //spremimo podatke u array da mozemo doci do njih kada kliknemo na button next
    grafovi.push(new Graf(imena, vrijednosti, my_title, x_label, y_label, i, max_duljina_osi, 
                                                                            imena_mjerenja));

    crtaj1(imena, vrijednosti, my_title, x_label, y_label, i, max_duljina_osi, imena_mjerenja);
  }

  //next button za mjenjanje izgleda grafa
  //prodjemo po svim grafovima u polju grafovi i na tim podacima pozovemo odgovarajuce funkcije
  $("body").append('<button id="next" type="submit">Next</button>');
  var brojac = 1;
  $("#next").on("click", function(){
    console.log(grafovi);
      if(brojac%3 == 0){
        for(var j = 0; j < grafovi.length; ++j)
          crtaj1(grafovi[j].imena, grafovi[j].vrijednosti, grafovi[j].my_title, 
                grafovi[j].x_label, grafovi[j].y_label, grafovi[j].i, grafovi[j].max_duljina_osi, 
                                                                         grafovi[j].imena_mjerenja);
        ++brojac;
      }
      else if(brojac%3 == 1){
        for(var j = 0; j < charts.length; ++j)
          crtaj2(grafovi[j].imena, grafovi[j].vrijednosti, grafovi[j].my_title, 
                grafovi[j].x_label, grafovi[j].y_label, grafovi[j].i, grafovi[j].max_duljina_osi,
                                                                            grafovi[j].imena_mjerenja);
        ++brojac;
      }
      else{
        for(var j = 0; j < charts.length; ++j)
          crtaj3(grafovi[j].imena, grafovi[j].vrijednosti, grafovi[j].my_title, 
                  grafovi[j].x_label, grafovi[j].y_label, grafovi[j].i, grafovi[j].max_duljina_osi,
                                                                         grafovi[j].imena_mjerenja);
        ++brojac;
      }
  });
  $("#next").css("font-size","30px").css("margin-left", "20px");
}
