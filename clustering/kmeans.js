const MAX_ITERATIONS = 50;
const dataset = [
    [21,0,33],
    [27,1,33],
    [22,1,34],
    [1,1,1],
    [3,1,1],
    [27,1,13],
    [31,1,18],
    [25,0,7],
    [8,1,2],
    [9,0,15],
    [0,0,12],
    [15,1,8],
    [1,0,22],
    [11,1,11],
    [1,0,31],
];
const K = 5;
const THRESHOLD = 1500;

/**
 * 
 * @param {*} visualizer 2 o 3(2D o 3D)
 * @param K numero di cluster
*/
function start(visualizer, K) {
    results = kMeansAlgorithm(dataset, K, MAX_ITERATIONS, THRESHOLD);
    if( visualizer === 2 ) {
        draw2d(dataset, results.centroids);
    } else {
        draw3d(dataset)
    }

    return results;
}

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
        
        prevMse = mse(tags)
        tags = assign(dataset, centroids);
        centroids = relocateCentroids(dataset, tags, k);

        if(mse(tags) > prevMse) { break; }
        iterations++;
    }

    const clusters = [];
    for (let i = 0; i < k; i++) {
      clusters.push(tags[i].observations);
    }

    const results = {
      clusters: clusters,
      centroids: centroids,
      iterations: iterations,
      converged: iterations <= MAX_ITERATIONS,
      mse : mse(tags)
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
    sum = 0;
    
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
    
    const centroids = [];

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
        let sum = 0;

        for(let j = 0; j < dataset.length; j++) {
            sum += dataset[j][i];
        }

        means.push(Math.round(sum/dataset.length));
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

    for(let i = 0; i < count; i++) {
        let sum = 0;
        let curCluster = tags[i];
        let curCentroid = tags[i].centroid;
        
        if(curCluster.observations.length > 0) {
            for(let j = 0; j < curCluster.observations.length; j++) {
                sum += getDistance(curCentroid, curCluster.observations[j]);
            }
        }
    }

    console.log(Math.pow(sum, 2))
    return Math.pow(sum, 2)
}

function draw3d(dataset) {

    var x = [];
    var y = [];
    var z = [];

    for(let i = 0; i < dataset.length; i++) {
        x[i] = dataset[i][0];
        y[i] = dataset[i][1];
        z[i] = dataset[i][2];
    }

    var data = [{
        x: x,
        y: y,
        z: z,
        mode: 'markers',
        type: 'scatter3d',
        marker: {
          color: 'red',
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

function draw2d(dataset, centroids) {

    var x = [];
    var z = [];

    for(let i = 0; i < dataset.length; i++) {
        x[i] = dataset[i][0];
        z[i] = dataset[i][2];
    }

    var xCentroids = [];
    var yCentroids = [];

    for(let i = 0; i < centroids.length; i++) {
        xCentroids[i] = centroids[i][0];
        yCentroids[i] = centroids[i][2];
    }
    
    var trace1 = {
        x: x,
        y: z,
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
