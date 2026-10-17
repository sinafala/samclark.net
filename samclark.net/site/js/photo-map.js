/* ============================================================================
   photo-map.js  —  samclark.net
   ============================================================================
   Leaflet map of where a set of photos was taken, linked both ways with the
   photo captions on the page.

   Page contract (see mtb.shtml, section #usa-2026, for a working example):

     <div class="sam-photomap" id="CANVAS-ID"
          data-photomap="DATA-ID"            id of the JSON <script> below
          data-thumbs="/site/media/thumbs/"  thumbnail directory
          data-file="west-coast-mtb-2026-"   file stem; photo N -> STEM N .jpg
          data-anchor="p2026-"></div>        photo block N has id ANCHOR N

     <script type="application/json" id="DATA-ID">
       { "photos":   [ {"n":1, "place":"...", "c":0}, ... ],   c = cluster index or null
         "clusters": [ {"lat":..,"lon":..,"place":"...","date":"YYYY-MM-DD",
                        "photos":[1,2,...]}, ... ] }
     </script>

     Caption links:  <a href="#map-N" class="sam-maplink" data-photo="N">Place</a>
       * click        -> scroll to the map, zoom to the spot, open its popup
       * URL #map-N   -> same thing on page load (shareable deep link)
     Popup thumbnails link back down to photo block #ANCHOR N.

   Requires Leaflet 1.9.x loaded before this file. Several maps per page are
   fine; caption links are routed to the map whose data lists that photo
   (a page with more than one map should use distinct photo numbers per map
   or give each map its own link prefix - not needed yet).
   ============================================================================ */

(function () {
  "use strict";

  function initPhotoMap(el) {
    var data  = JSON.parse(document.getElementById(el.dataset.photomap).textContent);
    var thumb = function (n) { return el.dataset.thumbs + el.dataset.file + n + ".jpg"; };
    var anchor = el.dataset.anchor;

    var osm = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 19,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>'
    });
    var topo = L.tileLayer("https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png", {
      maxZoom: 17,
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>, ' +
                   'style &copy; <a href="https://opentopomap.org" target="_blank">OpenTopoMap</a>'
    });

    var map = L.map(el, { layers: [osm], scrollWheelZoom: false });
    L.control.layers({ "Streets": osm, "Topographic": topo }, null, { position: "bottomleft" }).addTo(map);
    map.on("focus", function () { map.scrollWheelZoom.enable(); });
    map.on("blur",  function () { map.scrollWheelZoom.disable(); });

    var markers = [];
    var bounds  = [];

    function popupHtml(ci, current) {
      var c = data.clusters[ci];
      var h = '<div class="photomap-popup"><div class="photomap-title">' + c.place +
              '</div><div class="photomap-date">' + c.date + " &middot; " +
              c.photos.length + (c.photos.length === 1 ? " photo" : " photos") +
              '</div><div class="photomap-thumbs">';
      c.photos.forEach(function (n) {
        h += '<a href="#' + anchor + n + '" title="Go to photo ' + n + ' on the page"' +
             (n === current ? ' class="current"' : "") + '><img src="' + thumb(n) +
             '" alt="Photo ' + n + '" loading="lazy"></a>';
      });
      return h + "</div></div>";
    }

    data.clusters.forEach(function (c, ci) {
      var icon = L.divIcon({
        className: "photomap-pin",
        html: "<span>" + c.photos.length + "</span>",
        iconSize: [22, 22], iconAnchor: [11, 11], popupAnchor: [0, -10]
      });
      var m = L.marker([c.lat, c.lon], { icon: icon, title: c.place, riseOnHover: true })
               .addTo(map)
               .bindPopup(function () { return popupHtml(ci, m._current); },
                          { maxWidth: 260, minWidth: 180,
                            autoPanPaddingTopLeft: [50, 20],       // clear the zoom buttons
                            autoPanPaddingBottomRight: [20, 20] });
      m.on("popupclose", function () { m._current = null; });
      markers.push(m);
      bounds.push([c.lat, c.lon]);
    });
    map.fitBounds(bounds, { padding: [20, 20] });

    // photo number -> cluster index
    var clusterOf = {};
    data.photos.forEach(function (p) { if (p.c !== null) { clusterOf[p.n] = p.c; } });

    function show(n, scroll) {
      var ci = clusterOf[n];
      if (ci === undefined) { return false; }
      if (scroll) { el.scrollIntoView({ block: "center" }); }
      var m = markers[ci];
      m._current = n;
      map.setView(m.getLatLng(), Math.max(map.getZoom(), 10), { animate: false });
      m.openPopup();
      m.setPopupContent(popupHtml(ci, n));
      return true;
    }

    return { show: show, has: function (n) { return clusterOf[n] !== undefined; } };
  }

  function boot() {
    if (!window.L) { return; }                      // Leaflet failed to load
    var maps = [];
    document.querySelectorAll(".sam-photomap[data-photomap]").forEach(function (el) {
      maps.push(initPhotoMap(el));
    });
    function route(n, scroll) {
      for (var i = 0; i < maps.length; i++) {
        if (maps[i].has(n)) { return maps[i].show(n, scroll); }
      }
      return false;
    }

    document.addEventListener("click", function (e) {
      var a = e.target.closest("a.sam-maplink[data-photo]");
      if (!a) { return; }
      var n = parseInt(a.dataset.photo, 10);
      if (route(n, true)) {
        e.preventDefault();
        history.replaceState(null, "", "#map-" + n);
      }
    });

    var hit = /^#map-(\d+)$/.exec(location.hash);
    if (hit) { route(parseInt(hit[1], 10), true); }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
