const MAX_ITERATIONS = 50;
const dataset = [
    [21,0,33],
    [22,0,34],
    [29,0,30],
    [15,1,15],
    [17,1,12],
    [18,1,16],
    [6,1,5],
    [8,1,2],
    [7,1,4],
    [11, 0, 22],
    [4,1, 31],
    [23, 0, 32],
    [1, 1, 30],
    [1, 1, 29],
    [1, 0, 31],
    [1, 0, 30],
    [1, 0, 32]
];

const K = 3;
const THRESHOLD = 1500;

/**
 * 
 * @param {*} visualizer 2 o 3(2D o 3D)
 * @param K numero di cluster
 * @param dataSet
*/
function start(visualizer, K) {
    results = kMeansAlgorithm(dataset, K, MAX_ITERATIONS, THRESHOLD);
    if( visualizer === 2 ) {
        draw2d(results);
    } else {
        draw3d(results);
    }

    return results;
}

//kMeansAlgorithm(tmp, K, MAX_ITERATIONS, THRESHOLD);

/**
 * 
 * @param {*} dataset 
 * @param {*} k 
 * @param {*} maxIterations 
 * @param {*} threshold 
 */
 function kMeansAlgorithm(dataset, k, maxIterations, threshold) {

    // Si inizializzano i centroidi
    let centroids = getRandomCentroids(dataset, k);
    let tags = assign(dataset, centroids);
    let iterations = 0;
    while(iterations < maxIterations && mse(tags) > threshold) {
        
        centroids = relocateCentroids(dataset, tags, k);
        tags = assign(dataset, centroids);

        iterations++;
    }

    const clusters = [];
    for (let i = 0; i < k; i++) {
      clusters.push(tags[i].observations);
    }

    const results = {
      clusters: tags,
      k : clusters.length,
      centroids: centroids,
      iterations: iterations,
      converged: iterations < MAX_ITERATIONS,
      mse : mse(tags),
      threshold : threshold,
      observations : dataset
    };
    
    console.log(results);
    return results;
}

/**
 * Genera un valore random tra min e max
 * @param {*} min 
 * @param {*} max 
 * @returns min <= rand < max
 */
function randomBetween(min, max) {
    return Math.floor(
        Math.random() * (max - min) + min
    );
}

/**
 * Distanza euclidea
 * @param point1
 * @param point2
 */
function getDistance(point1, point2) {
   var sum = 0;
    
    for(let i = 0; i < point1.length; i++) {
        sum += Math.pow((point1[i] - point2[i]), 2)
    }

    return Math.sqrt(sum);
}

/**
 * Genera k centroidi con coordinate comprese tra i bound [max e min] delle feature dei punti,
 * nulla di complesso ma difficile da spiegare, serve per convergere più in fretta
 * @param {*} dataset 
 * @param {*} k 
 */
function getRandomCentroids(dataset, k) {
    
    var centroids = [];

    // Array dei massimi e dei minimi
    const maxs = [];
    const mins = [];

    // Si scansiona il dataset alla ricerca dei valori massimi e minimi per ogni feature
    for(let i = 0; i < dataset[0].length; i++) {
        let max = Number.MIN_SAFE_INTEGER;
        let min = Number.MAX_SAFE_INTEGER;

        for(let j = 0; j < dataset.length; j++) {
            max = dataset[j][i] > max ? dataset[j][i] : max;
            min = dataset[j][i] < min ? dataset[j][i] : min;
        }

        maxs.push(max);
        mins.push(min);
    }

    var centroid;
    for(let i = 0; i < k; i++) {
        centroid = [];

        for(let j = 0; j < maxs.length; j++) {
            let min = mins[j];
            let max = maxs[j];
            centroid.push(randomBetween(min, max));
        }

        centroids.push(centroid);
    }

    return centroids;
}

/**
 * Genera una reference dai punti verso il centroide del loro cluster
 * @param dataset {*}
 * @param centroids {*}
 */
function assign(dataset, centroids) {
    // L'oggetto che serve ha una reference al centroide ed ai punti che sono nel suo cluster
    const tags = {}
    for(let i = 0; i < centroids.length; i++) {
        tags[i] = {
            observations : [],
            centroid : centroids[i]
        }
    }

    // Si scorrono le osservazioni e si assegnano al centoride più vicino
    for(let i = 0; i < dataset.length; i++) {
        const obs = dataset[i];
        let minDistance = Number.MAX_SAFE_INTEGER;
        let indexOfClosestCentroid = 0;

        for(let j = 0; j < centroids.length; j++) {
            let distance = getDistance(obs, centroids[j]);
            if(distance < minDistance) {
                minDistance = distance;
                indexOfClosestCentroid = j;
            }
        }

        tags[indexOfClosestCentroid].observations.push( obs );
    }

    return tags;
}

/**
 * Calcola la media dei punti
 */
function clusterMean(dataset) {
    
    const means = [];
    for(let i = 0; i < dataset[0].length; i++) {
        var sum = 0;

        for(let j = 0; j < dataset.length; j++) {
            sum += Number(dataset[j][i]);
        }

        means.push((sum/dataset.length).toFixed(2));
    }

    return means;
}

/**
 * Ricolloca i centroidi
 * @param dataset
 * @param tags
 * @param k
 */
function relocateCentroids(dataset, tags, k) {
    let centroid;
    const newCentroids = [];

    for(const k in tags) {    
        // Se ci sono elementi nel cluster del centroide si calcola la media geometrica dei punti in tale cluster
        if(tags[k].observations.length > 0) {
            centroid = clusterMean(tags[k].observations);
        } else {
            // Questo punto è delicato, grazie a implementazioni note ci si è accorti che se
            // non ci sono osservazioni che appartengono ad un centroide è bene riposizionarlo.
            centroid = getRandomCentroids(dataset, 1)[0];
        }
        newCentroids.push(centroid);
    }

    return newCentroids;
}

/**
 * 
 * @param {*} dataset 
 * TO DO: rivedere
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
 * Mean squared error
 * 
 * @param tags osservazioni e rispettivi centoridi
 */
 function mse(tags) {
    // Number of object inside tags
    var count = 0;

    for(var prop in tags) {
        if(tags.hasOwnProperty(prop))
            ++count;
    }

    var sum = 0;
    for(let i = 0; i < count; i++) {
        var curCluster = tags[i];
        var curCentroid = tags[i].centroid;
        
        if(curCluster.observations.length > 0) {
            for(let j = 0; j < curCluster.observations.length; j++) {
                sum += getDistance(curCentroid, curCluster.observations[j]);
            }
        }
    }

    return Number(Math.pow(sum, 2)).toFixed(2);
}

function draw3d(results) {

    var centroids = results.centroids;
    var obs = results.observations;

    var x = [];
    var y = [];
    var z = [];

    var xCentroids = [];
    var yCentroids = [];
    var zCentroids = [];

    for(let i = 0; i < centroids.length; i++) {
        xCentroids[i] = centroids[i][0];
        yCentroids[i] = centroids[i][1];
        zCentroids[i] = centroids[i][2];
    }

    for(let i = 0; i < obs.length; i++) {
        x[i] = obs[i][0];
        y[i] = obs[i][1];
        z[i] = obs[i][2];
    }

    var data = [{
        x: x,
        y: y,
        z: z,
        mode: 'markers',
        type: 'scatter3d',
        name: "obs",
        marker: {
          color: 'red',
          size: 3
        }
    },{
        x: xCentroids,
        y: yCentroids,
        z: zCentroids,
        mode: 'markers',
        type: 'scatter3d',
        name: "centroidi",
        marker: {
        color: 'black',
        size: 3
        }
    
    },{
        alphahull: 7,
        opacity: 0.1,
        type: 'mesh3d',
        x: x,
        y: y,
        z: z
    }];

    var layout = {
        autosize: true,
        height: 300,
        scene: {
            aspectratio: {
                x: 5,
                y: 5,
                z: 5
            },
            camera: {
                center: {
                    x: 0,
                    y: 0,
                    z: 0
                },
                eye: {
                    x: 5.25,
                    y: 2.25,
                    z: 2.25
                },
                up: {
                    x: 0,
                    y: 0,
                    z: 1
                }
            },
            xaxis: {
                type: 'linear',
                zeroline: true
            },
            yaxis: {
                type: 'linear',
                zeroline: true
            },
            zaxis: {
                type: 'linear',
                zeroline: true
            }
        },
        title: '3d point clustering',
        width: 477
    };

    Plotly.newPlot('root', data, layout);
}

function draw2d(results) {

    var centroids = results.centroids;
    var obs = results.observations;
    
    var x = [];
    var y = [];
    for(let i = 0; i < obs.length; i++) {
        x[i] = obs[i][0];
        y[i] = obs[i][2];
    }
    
    var xCentroids = [];
    var yCentroids = [];

    for(let i = 0; i < centroids.length; i++) {
        xCentroids[i] = centroids[i][0];
        yCentroids[i] = centroids[i][2];
    }
    
    var trace1 = {
        x: x,
        y: y,
        mode: 'markers',
        type: 'scatter',
        name: 'obs'
      };
      
      var trace2 = {
        x: xCentroids,
        y: yCentroids,
        mode: 'markers',
        type: 'scatter',
        name: 'centroidi'
      };
      
      var data = [trace1, trace2];
      
      Plotly.newPlot('root', data);
}
