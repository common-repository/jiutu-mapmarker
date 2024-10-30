importScripts('https://unpkg.com/supercluster@7.1.2/dist/supercluster.min.js');
let index;
self.onmessage = function(e) {

    if (e.data.type == 'getleaves') {
        return postMessage({
            type: 'getleaves',
            list: index.getLeaves(e.data.id),
            rawdata: e.data.rawdata
        })
    }
    if (e.data.geojson.type == 'FeatureCollection') {
        index = new Supercluster({
            radius: 60,
            maxZoom: 17,
        }).load(e.data.geojson.features);
        // console.log(e.data.markers);
        // e.data.markers.forEach(function(t) {
        //     return t.remove();
        // });

        return postMessage({
            ready: true,
            cluster: index.getClusters([-180, -90, 180, 90], Math.floor(e.data.zoom))
        })
    }

};