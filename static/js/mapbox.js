!(function() {
    jQuery(document).on('pjax:complete', function() {
        if (null != document.querySelector("#jiutu_mapmarker_map")) {
            mapbox_start()
        }
    });

    function mapbox_init(jiutu_mapmarker_data) {
        var geojson = jiutu_mapmarker_data.mapmarker_gojson;
        var map_settings = jiutu_mapmarker_data.jiutu_map_settings;
        jQuery("#jiutu_mapmarker_map").css({
            "border": map_settings.map_size.bottom + 'px solid' + map_settings.map_size.color,
            "height": map_settings.map_size.top + 'px',
            "width": map_settings.map_size.right + '%',
        });

        jQuery("#map_desc").html(map_settings.map_desc);
        if (geojson == '') {
            geojson = []
        }
        var jsonArrar = new Array();
        geojson.forEach(function(value, index) {
            jsonArrar[index] = {
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: [parseFloat(value.map_data.longitude), parseFloat(value.map_data.latitude)]
                },
                properties: {
                    address: value.address,
                    title: value.title,
                    description: value.description,
                    posts: value.posts,
                    image: value.images,
                    map_time: value.map_time,
                    markerColour: value.markerColour,
                }
            };
        });

        jsonArrar = {
            type: "FeatureCollection",
            features: jsonArrar,
        }
        const worker = new Worker('/wp-content/plugins/jiutu-mapmarker/static/js/worker.js');
        mapboxgl.markers = [];
        var map_config = {
            container: "jiutu_mapmarker_map",
            accessToken: 'pk.eyJ1Ijoiaml1dHUiLCJhIjoiY2w0dHdtYndtMWY5ZjNkbHBjbG4ybjl3MyJ9.sZGgLHwVLr5AV0vCtuvN_A',
            style: 'mapbox://styles/jiutu/cl4tuyb9y000o14rmpgxchscd',
            center: [parseFloat(map_settings.map_center.longitude), parseFloat(map_settings.map_center.latitude)],
            minZoom: parseInt(map_settings.map_zoom.width),
            maxZoom: parseInt(map_settings.map_zoom.height),
            zoom: parseInt(map_settings.map_initial_zoom),
            // worldview: 'CN'
        }
        var map = new mapboxgl.Map(map_config);
        if (map_settings.map_zoom_control != "display") {
            map.addControl(new mapboxgl.NavigationControl(), map_settings.map_zoom_control);
        }
        map.addControl(new MapboxLanguage({
            defaultLanguage: map_settings.map_language
        }));

        if (map_settings.map_location != "display") {
            map.addControl({
                onAdd: function(map) {
                    return (
                        (this._btn = document.createElement("button")),
                        (this._btn.className = "mapboxgl-ctrl-icon mapbox-gl-draw_polygon"),
                        (this._btn.type = "button"),
                        (this._btn.title = '归位'),
                        (this._btn.onclick = function() {
                            map.flyTo({ center: [108.14, 33.87], zoom: 3 });
                        }),
                        (this._container = document.createElement("div")),
                        (this._container.className = "mapboxgl-ctrl-group mapboxgl-ctrl"),
                        this._container.appendChild(this._btn),
                        this._container
                    );
                },
            }, map_settings.map_location);
        }


        map.on("load", function(t) {
            if (map_settings.map_type == 'globe') {
                map.setStyle(
                    Object.assign({}, map.getStyle(), { projection: { name: 'globe' } })
                )
                map.setFog({
                    "color": map_settings.map_star.color.color,
                    "high-color": map_settings.map_star.color.high_color,
                    "space-color": map_settings.map_star.color.space_color,
                    "horizon-blend": parseFloat(map_settings.map_star.fog.height),
                    "star-intensity": parseFloat(map_settings.map_star.fog.width)
                });
            }

            worker.postMessage({
                geojson: jsonArrar,
                zoom: Math.floor(map.getZoom()),
            });
            worker.onmessage = function(e) {
                if (e.data.ready) {
                    (mapboxgl.clusterData = {
                        type: "FeatureCollection",
                        features: e.data.cluster,
                    })
                    updateMarkers();
                    jQuery("#jiutu_mapmarker_map").addClass("is-loaded")
                }
            };
        });

        map.on("zoomend", function(t) {
            (mapboxgl.markers.forEach(function(t) {
                return t.remove();
            }));
            worker.postMessage({
                geojson: jsonArrar,
                zoom: Math.floor(map.getZoom())
            });
            worker.onmessage = function(e) {
                if (e.data.ready) {
                    (mapboxgl.clusterData = {
                        type: "FeatureCollection",
                        features: e.data.cluster,
                    })
                    updateMarkers()
                }
            };
        });

        function updateMarkers() {
            for (var a, i = mapboxgl.clusterData.features[Symbol.iterator](); !(t = (a = i.next()).done); t = !0) {
                var o = a.value;
                o.properties.cluster ? addClusterMarker(o) : addPhotoMarker(o);
            }
        }


        function addClusterMarker(data) {
            worker.postMessage({
                type: 'getleaves',
                id: data.properties.cluster_id,
                rawdata: data
            });
            worker.onmessage = function(e) {
                if (e.data.type == "getleaves") {
                    t = (e.data.list, document.createElement("div"));
                    (t.className = "marker cluster");
                    t.addEventListener("click", function(t) {
                        map.fitBounds(geojsonExtent({
                            type: "FeatureCollection",
                            features: e.data.list,
                        }), {
                            padding: 0.32 * map.getContainer().offsetHeight,
                        });
                    });
                    (t.dataset.cardinality = Math.min(9, e.data.rawdata.properties.point_count));
                    var n = new mapboxgl.Marker(t).setLngLat(e.data.rawdata.geometry.coordinates).addTo(map);
                    return mapboxgl.markers.push(n), n;
                }
            };

        }



        function clusterDidClick(t, data) {
            worker.postMessage({
                type: 'getleaves',
                id: data.properties.cluster_id,
                rawdata: data
            });
            worker.onmessage = function(e) {
                if (e.data.type == "getleaves") {
                    var n = {
                        type: "FeatureCollection",
                        features: e.data.list,
                    };
                    map.fitBounds(geojsonExtent(n), {
                        padding: 0.32 * map.getContainer().offsetHeight,
                    });
                }
            };
        }


        function addPhotoMarker(t) {
            var e = document.createElement("div");
            (e.className = "marker"),
            e.style.setProperty("--photo", 'url("'.concat(t.properties.image, '"'));
            e.style.setProperty("background", 'linear-gradient(180deg, ' + t.properties.markerColour + ' 0,' + t.properties.markerColour + ')');
            var n = '<strong style="font-size:16px"> ' + t.properties.title + "</strong>";
            n += '<br>' + t.properties.description;

            if (t.properties.posts) {
                var conut = 1;
                for (var a = t.properties.posts.length - 1; 0 <= a; a--) {
                    n += '<p><a target="_blank" href="' + Object.keys(t.properties.posts[a]) + '">' + conut + '、' + Object.values(t.properties.posts[a]) + "</a></p>";
                    conut++;
                }
            }
            n += '<span style="float: right;">《 ' + t.properties.address + ' 》' + t.properties.map_time + '</span>';
            var a = new mapboxgl.Marker(e).setLngLat(t.geometry.coordinates).setPopup(
                new mapboxgl.Popup({
                    closeButton: false,
                    offset: 0,
                    maxWidth: '340px'
                }).setHTML(n)
            ).addTo(map);
            return mapboxgl.markers.push(a), a;
        }
    }

    if (null != document.querySelector("#jiutu_mapmarker_map")) {
        mapbox_start()
    }

    function mapbox_start() {
        fetch("/wp-admin/admin-ajax.php?action=jiutu_mapmarker_geojson_api").then(function(t) {
            return t.json().then(function(t) {
                mapbox_init(t);
            });
        });
    }
})();