const CACHE="dice-game-v1";
const SHELL=["./","./index.html","./app.js","./cpu.js","./style.css","./manifest.webmanifest","./icon.svg","./icon-192.png","./icon-512.png","./rules.html"];
self.addEventListener("install",event=>{event.waitUntil(caches.open(CACHE).then(c=>c.addAll(SHELL)).catch(()=>{}).then(()=>self.skipWaiting()))});
self.addEventListener("activate",event=>{event.waitUntil(caches.keys().then(keys=>Promise.all(keys.filter(k=>k!==CACHE).map(k=>caches.delete(k)))).then(()=>self.clients.claim()))});
// Network first so every deploy is live immediately; the cached app shell is only an offline fallback. The API is never cached.
self.addEventListener("fetch",event=>{
  const req=event.request,url=new URL(req.url);
  if(req.method!=="GET"||url.origin!==self.location.origin||url.pathname.includes("/api/"))return;
  event.respondWith(fetch(req).then(res=>{
    if(res.ok&&res.type==="basic"){const copy=res.clone();caches.open(CACHE).then(c=>c.put(req,copy)).catch(()=>{})}
    return res;
  }).catch(()=>caches.match(req,{ignoreSearch:true}).then(hit=>hit||(req.mode==="navigate"?caches.match("./index.html"):Response.error()))));
});
