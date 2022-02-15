const k = 2;

class User {
    constructor(features, id) {
        this.features = features;
        this.id = id;
        this.centroid = null;
    }
}

class Centroid {
    constructor(features, id) {
        this.features = features;
        this.listOfUsers = [];
        this.id = id;
    }
}

features1 = [3, "SPEAKER", 25];
var user1 = new User(features1, 1);

features2 = [30, "PRESENTER", 6];
var user2 = new User(features2, 2);

features3 = [8, "SPEAKER", 40];
var user3 = new User(features3, 3);

features4 = [27, "PRESENTER", 5];
var user4 = new User(features4, 4);

features5 = [4, "SPEAKER", 30];
var user5 = new User(features5, 5);

features6 = [51, "SPEAKER", 2];
var user6 = new User(features6, 6);

features7 = [6, "PRESENTER", 11];
var user7 = new User(features7, 7);

features8 = [2, "PRESENTER", 1];
var user8 = new User(features8, 8);

features9 = [27, "SPEAKER", 21];
var user9 = new User(features9, 9);
;

const data = [user1, user2, user3, user4, user5, user6, user7,user8, user9];
/**
 * Traduzione di attributi non numerici in dominio numerico
 */
 function domainTranslation(users) {

    users.forEach(user => {
        
        for(let i = 0; i < user.features.length; i++) {
            if( user.features[i] === "SPEAKER" ) {
                user.features[i] = 0;
                
            } else if(user.features[i] === "PRESENTER") {
                user.features[i] = 1;
            }
        }
    });
}

/**
 * Distanza euclidea
 * @param point1
 * @param point2
 */
 function getDistance(point1, point2) {
    
    sum = 0;
    
    for(let i = 0; i < point1.features.length; i++) {
        sum += (point1.features !== undefined && point2.features !== undefined) ? Math.pow((point1.features[i] - point2.features[i]), 2) : 0;
    }

    return Math.sqrt(sum);
}

/**
 * Generazione random di k centroidi entro un bound dato dai valori estermi [min e max]
 * di ogni attributo (per motivi di converegenza più veloce)
 * 
 * @param data
 * @param k 
 */
 function randomCentroids( data, k ) {

    var centroids = new Array();
    // Si ordina la lista di utenti secondo il valore
    // della prima (0) e terza (2) features in quanto
    // e si estrae il massimo ed il minimo

    // O(n logn) + O(n logn)
    // Gli array numerici usano la libreria Std di C++ che utilizza quick sort
    // Gli array non numerici usano mergeSort ma dipende dal browser

    data.sort(function(a,b) {return b.features[0] - a.features[0]})
    var maxAtt0 = data[0].features[0];
    var minAtt0 = data[data.length-1].features[0];

    data.sort(function(a,b) {return b.features[2] - a.features[2]})
    var maxAtt2 = data[0].features[2];
    var minAtt2 = data[data.length-1].features[2];

    // Generazione di k centroidi rappresentabili come punti
    // con le stesse feature degli utenti
    for(let i = 0; i < k; i++) {
        var features = [Math.round(Math.random() * (maxAtt0 - minAtt0 + 1) + minAtt0), Math.round(Math.random()), Math.round(Math.random() * (maxAtt2 - minAtt2 + 1) + minAtt2)];
        var centroid = new Centroid(features, i)
        centroids.push(centroid);
    }

    return centroids;
}

/**
 * Assegnamento dei punti al centroide più vicino 
 */
function assignToCluster(data, centroids, k) {
    
    // Numero di osservazioni
    var n = data.length;

    // Si scorrono le osservazioni
    for(let i = 0; i < n; i++) {
        var min = Number.MAX_SAFE_INTEGER;
        var curData = data[i];

        // Si scorrono i centroidi e si assegna
        // al dato il centroide più vicino
        for(let j = 0; j < k; j++) {
            var curCentroid = centroids[j];
            var distance = getDistance(curData, curCentroid);
            
            if(distance < min) {
                curData.centroid = curCentroid;
                min = distance;

            }
        }
    }
    
    // Si inseriscono i dati nella lista mantenuta dai centroidi
    for(let i = 0; i < n; i++) {
        var clusterId = data[i].centroid.id;
        if(!centroids[clusterId].listOfUsers.includes(data[i]) ) {
            centroids[clusterId].listOfUsers.push(data[i]);
        }
    }

    var newCentroids = new Array();
    
    // Si pulisce la lista dei centroidi
    for(let i = 0; i < k; i++) {
        var curCentroid = centroids[i];
        for(let j = 0; j < curCentroid.listOfUsers.length; j++) {
            var curData = curCentroid.listOfUsers[j];

            if(curData.centroid.id !== curCentroid.id) {
                curCentroid.listOfUsers.splice(j);
            }
        }
        newCentroids.push(curCentroid);
    }
    
    return newCentroids;
}

/**
 * Ricolloca i centroidi al centro del loro cluster
 */
 function reLocateCentroids(centroids) {
    
    for(let i = 0; i < k; i++) {
        centroid = centroids[i];
        centroid.features = calculateMean(centroid.listOfUsers, centroid.features);
    }
    
}

function calculateMean(listOfUsers, currentFeatures) {
    var n = listOfUsers.length;

    if(n !== 0) {
        var sum1 = 0;
        var sum2 = 0;
        var sum3 = 0;

        var newFeatures = [];

        for(let i = 0; i < n; i++) {
            var user = listOfUsers[i];
            sum1 += user.features[0];
            sum2 += user.features[1];
            sum3 += user.features[2];
        }
        
        newFeatures.push(Math.round(sum1/n), Math.round(sum2/n), Math.round(sum3/n));
    
        return newFeatures;
        
    } else {
        return currentFeatures;
    }
}

/**
 * Mean squared error
 * 
 * @param data osservazioni
 * @param centroids centoridi parziali
 */
function mse(data) {

    var sum = 0;
    for(let i = 0; i < data.length; i++) {
        sum += getDistance(data[i], data[i].centroid);
    }

    return Math.round(Math.pow(sum, 2));
}

function printResult(data, centroids) {
    console.log(centroids);
    console.log(data);
}

/**
 * 
 * @param {*} data osservazioni
 * @param {*} k numero di cluster
 * @param {*} maxIteration numero massimo di iterazioni
 * @param {*} threshold soglia dell'errore
 */
function kMeanAlgorithm(data, k, maxIterations, threshold) {
    
    domainTranslation(data);
    var centroids = randomCentroids(data, k);

    var i = 0;
    while( i < maxIterations) {
       
        centroids = assignToCluster(data, centroids, k);
        
        // Shallow copy
        var prevMse = mse(data, centroids) ;
        console.log(prevMse)

        reLocateCentroids(centroids);
        
        if( mse(data,centroids) < threshold || mse(data, centroids) === prevMse) {
            console.log("raggiunta convergenza")
            console.log("Numero di iterazioni " + i);
            break;
        }
        
        i++
    
    } 

    //printResult(data, centroids);
    plot2D(data, centroids);
}

kMeanAlgorithm(data, 2, 50);

function plot(observations) {
    
    xs = [];
    ys = [];
    zs = [];

    n = 6;

    for(let i = 0; i < n; i++) {
        xs.push(observations[i].features[0]);
        ys.push(observations[i].features[1]);
        zs.push(observations[i].features[2]);
    }

    var d = [{
        x : xs,
        y : ys,
        z : zs,
        mode: 'markers',
        type: 'scatter3d',
        marker: {
            color: 'rgb(23, 190, 207)',
            size: 4
        }
    },{
        alphahull: 7,
        opacity: 0.1,
        type: 'mesh3d',
        x : xs,
        y : ys,
        z : zs,
    }];

    var layout = {
        autosize: true,
        height: 400,
        scene: {
            aspectratio: {
                x: 1,
                y: 1,
                z: 1
            },
            camera: {
                center: {
                    x: 0,
                    y: 0,
                    z: 0
                },
                eye: {
                    x: 0.5,
                    y: 0.5,
                    z: 0.5
                },
                up: {
                    x: 0,
                    y: 0,
                    z: 1
                }
            },
            xaxis: {
                type: 'linear',
                zeroline: false
            },
            yaxis: {
                type: 'linear',
                zeroline: false
            },
            zaxis: {
                type: 'linear',
                zeroline: false
            },
        },
        title: '3d Clustering',
        width: 500
    };

    Plotly.newPlot('root', d, layout);
}

function plot2D(observations, centroids) {
    
    xs = [];
    ys = [];
    zs = []
    
    cx = [];
    cy = [];

    n = 6;

    for(let i = 0; i < n; i++) {
        xs.push(observations[i].features[0]);
        ys.push(observations[i].features[1]);
        zs.push(observations[i].features[2]);
    }


    for(let i = 0; i < centroids.length; i++) {
        cx.push(centroids[i].features[0]);
        cy.push(centroids[i].features[2]);
    }

    var trace1 = {
        x: xs,
        y: zs,
        mode: 'markers',
        type: 'scatter',
        name: 'osservazione',
        marker: { size: 12 }
      };
      
      var trace2 = {
        x: cx,
        y: cy,
        mode: 'markers',
        type: 'scatter',
        name: 'centroide',
        marker: { size: 12 }
      };
      
      var data = [ trace1, trace2 ];
      
      var layout = {
        xaxis: {
          range: [ 0.75, 5.25 ]
        },
        yaxis: {
          range: [0, 8]
        },
        title:'Data Labels Hover'
      };
      
      Plotly.newPlot('root', data, layout, {autoscale : true});
      
}