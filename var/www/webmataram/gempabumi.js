/**
 * Gempabumi Page - BMKG API Integration
 * Auto-sync dengan data gempa bumi real-time dari BMKG Pusat
 * Sumber: https://data.bmkg.go.id/DataMKG/TEWS/
 */
class GempaBumiPage {
    constructor() {
        this.config = { proxyUrl: 'api/gempabumi_proxy.php', updateInterval: 120000, itemsPerPage: 15 };
        this.state = {
            currentPage: 1, totalPages: 1, totalRecords: 0,
            allData: [], filteredData: [], latestQuake: null,
            filters: { period: 'all', magnitude: '0', region: 'all', depth: 'all', search: '' },
            isLoading: false, lastUpdate: null,
        };
        this.init();
    }
    init() { this.setupEventListeners(); this.loadAll(); this.setupAutoRefresh(); }

    setupEventListeners() {
        document.getElementById('apply-filter')?.addEventListener('click', () => this.applyFilters());
        document.getElementById('refresh-data')?.addEventListener('click', () => this.loadAll(true));
        document.getElementById('prev-page')?.addEventListener('click', () => { if (this.state.currentPage > 1) { this.state.currentPage--; this.renderTable(); } });
        document.getElementById('next-page')?.addEventListener('click', () => { if (this.state.currentPage < this.state.totalPages) { this.state.currentPage++; this.renderTable(); } });
        ['period-filter','magnitude-filter','region-filter','depth-filter'].forEach(id => {
            document.getElementById(id)?.addEventListener('change', (e) => { this.state.filters[id.replace('-filter','')] = e.target.value; });
        });
        document.getElementById('location-search')?.addEventListener('input', (e) => {
            clearTimeout(this._st); this._st = setTimeout(() => { this.state.filters.search = e.target.value.toLowerCase(); this.applyFilters(); }, 400);
        });
        document.getElementById('export-data')?.addEventListener('click', () => this.exportCSV());
    }

    async loadAll(f = false) {
        if (this.state.isLoading && !f) return;
        this.state.isLoading = true; this.showLoading();
        try {
            // Sequential fetch to avoid BMKG rate limiting
            const r1 = await fetch(this.config.proxyUrl+'?type=terkini&_='+Date.now());
            const d1 = await r1.json();
            
            const r3 = await fetch(this.config.proxyUrl+'?type=terbaru&_='+Date.now());
            const d3 = await r3.json();
            
            const r2 = await fetch(this.config.proxyUrl+'?type=dirasakan&_='+Date.now());
            const d2 = await r2.json();

            const l1 = d1.success ? this.parseList(d1.data?.Infogempa?.gempa||[]) : [];
            const l2 = d2.success ? this.parseList(d2.data?.Infogempa?.gempa||[]) : [];
            const lr = d3.success ? d3.data?.Infogempa?.gempa : null;
            this.state.allData = this.mergeData(l1,l2).sort((a,b)=>new Date(b.DateTime)-new Date(a.DateTime));
            this.state.latestQuake = lr ? this.parseOne(lr) : (this.state.allData[0] || null);
            this.state.lastUpdate = new Date();
            this.renderLatest(); this.applyFilters(); this.updateStats(); this.updateTimestamp();
        } catch(e) { console.error(e); this.showError(); }
        finally { this.state.isLoading = false; }
    }

    parseList(a) { return Array.isArray(a) ? a.map(g=>this.parseOne(g)).filter(Boolean) : []; }
    parseOne(g) {
        try {
            const c = (g.Coordinates||'').split(',').map(Number);
            const w = g.Wilayah||''; const d = g.Dirasakan||'';
            return {
                Tanggal: g.Tanggal||'', Jam: g.Jam||'', DateTime: g.DateTime||'',
                Lintang: g.Lintang||'', Bujur: g.Bujur||'',
                Latitude: c[0]||0, Longitude: c[1]||0,
                Magnitude: parseFloat(g.Magnitude)||0,
                Kedalaman: parseInt(g.Kedalaman)||0,
                KedalamanStr: g.Kedalaman||'', Wilayah: w, Potensi: g.Potensi||'',
                Dirasakan: d, SkalaMMI: this.parseMMI(d),
                isNTB: !!(w.match(/lombok|sumbawa|mataram|ntb|bima|dompu|sape|selong|praya/i)),
            };
        } catch { return null; }
    }
    parseMMI(d) {
        if (!d) return null;
        const m = d.match(/([IV]+)/g); if (!m) return null;
        const r = {I:1,V:5,X:10,L:50,C:100};
        return Math.max(...m.map(s=>{let t=0;for(let i=0;i<s.length;i++){const c=r[s[i]]||0;t+=c<(r[s[i+1]]||0)?-c:c;}return t;}));
    }
    mergeData(a,b) { const m=new Map();[...a,...b].forEach(i=>{const k=i.DateTime+'-'+i.Magnitude+'-'+i.Wilayah;if(!m.has(k)||i.Dirasakan)m.set(k,i);});return Array.from(m.values());}

    applyFilters() {
        let f = [...this.state.allData]; const n = new Date();
        if (this.state.filters.period==='today') {const s=new Date(n.getFullYear(),n.getMonth(),n.getDate());f=f.filter(e=>new Date(e.DateTime)>=s);}
        else if (this.state.filters.period==='week') f=f.filter(e=>new Date(e.DateTime)>=new Date(n-7*86400000));
        else if (this.state.filters.period==='month') f=f.filter(e=>new Date(e.DateTime)>=new Date(n-30*86400000));
        const mM=parseFloat(this.state.filters.magnitude);if(mM>0)f=f.filter(e=>e.Magnitude>=mM);
        if(this.state.filters.region==='ntb')f=f.filter(e=>e.isNTB);
        if(this.state.filters.depth!=='all')f=f.filter(e=>this.state.filters.depth==='shallow'?e.Kedalaman<70:this.state.filters.depth==='intermediate'?e.Kedalaman>=70&&e.Kedalaman<=300:e.Kedalaman>300);
        if(this.state.filters.search){const q=this.state.filters.search;f=f.filter(e=>e.Wilayah.toLowerCase().includes(q)||e.Lintang.includes(q));}
        f.sort((a,b)=>new Date(b.DateTime)-new Date(a.DateTime));
        this.state.filteredData=f;this.state.totalRecords=f.length;
        this.state.totalPages=Math.ceil(f.length/this.config.itemsPerPage)||1;this.state.currentPage=1;this.renderTable();
    }

    renderTable() {
        const tb=document.getElementById('earthquake-data');if(!tb)return;
        document.getElementById('loading-state')?.classList.add('hidden');document.getElementById('earthquake-table')?.classList.remove('hidden');
        const si=(this.state.currentPage-1)*this.config.itemsPerPage,ei=Math.min(si+this.config.itemsPerPage,this.state.totalRecords);
        const pd=this.state.filteredData.slice(si,ei);
        if(!pd.length){tb.innerHTML='<tr><td colspan="7" class="px-6 py-12 text-center text-slate-500"><span class="material-symbols-outlined text-3xl block mb-2">search_off</span>Tidak ada data gempa ditemukan</td></tr>';}
        else{tb.innerHTML=pd.map((e,i)=>this.row(e,si+i+1)).join('');}
        this.updatePagination();
    }
    row(e,i) {
        const mc=this.magClass(e.Magnitude);
        return '<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">'
        +'<td class="px-4 py-3 text-sm text-slate-500">'+i+'</td>'
        +'<td class="px-4 py-3 whitespace-nowrap"><div class="text-sm font-medium">'+this.fmtWIB(e.DateTime)+'</div><div class="text-xs text-slate-400">'+this.timeAgo(e.DateTime)+'</div></td>'
        +'<td class="px-4 py-3"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold '+mc+'">M'+e.Magnitude.toFixed(1)+'</span></td>'
        +'<td class="px-4 py-3 text-sm">'+e.KedalamanStr+'</td>'
        +'<td class="px-4 py-3 text-sm text-slate-500 whitespace-nowrap">'+e.Lintang+'<br>'+e.Bujur+'</td>'
        +'<td class="px-4 py-3 text-sm max-w-xs"><div class="font-medium">'+e.Wilayah+'</div>'+(e.Potensi?'<div class="text-xs text-slate-400">'+e.Potensi+'</div>':'')+'</td>'
        +'<td class="px-4 py-3 text-sm">'+(e.Dirasakan?'<span class="text-amber-600 font-medium">'+e.Dirasakan+'</span>':'<span class="text-slate-400">-</span>')+(e.SkalaMMI?'<div class="text-xs text-slate-400">MMI '+e.SkalaMMI+'</div>':'')+'</td></tr>';
    }
    renderLatest() {
        const e=this.state.latestQuake,el=document.getElementById('latest-quake');
        if(!e||!el)return;
        el.innerHTML='<div class="flex items-start gap-4"><div class="w-16 h-16 rounded-xl flex items-center justify-center text-white text-2xl font-bold bg-blue-600">M'+e.Magnitude.toFixed(1)+'</div>'
        +'<div class="flex-1"><div class="text-lg font-bold">'+this.fmtWIB(e.DateTime)+'</div><div class="text-sm text-slate-600 mt-1">'+e.Wilayah+'</div>'
        +'<div class="flex flex-wrap gap-3 mt-2 text-sm"><span class="inline-flex items-center gap-1 text-slate-500"><span class="material-symbols-outlined text-sm">straighten</span>'+e.KedalamanStr+'</span>'
        +'<span class="inline-flex items-center gap-1 text-slate-500"><span class="material-symbols-outlined text-sm">pin_drop</span>'+e.Lintang+', '+e.Bujur+'</span></div></div>'
        +'<a href="https://www.bmkg.go.id/gempabumi/gempabumi-dirasakan.bmkg" target="_blank" class="text-blue-600 text-sm flex items-center gap-1">Detail <span class="material-symbols-outlined text-sm">open_in_new</span></a></div>';
        document.getElementById('latest-quake-container')?.classList.remove('hidden');
    }
    updateStats() {
        const d=this.state.allData;if(!d.length)return;
        const n=new Date(),ts=new Date(n.getFullYear(),n.getMonth(),n.getDate()),wa=new Date(n-7*86400000);
        document.getElementById('total-count')&&(document.getElementById('total-count').textContent=d.length);
        document.getElementById('today-count')&&(document.getElementById('today-count').textContent=d.filter(e=>new Date(e.DateTime)>=ts).length);
        document.getElementById('week-count')&&(document.getElementById('week-count').textContent=d.filter(e=>new Date(e.DateTime)>=wa).length);
        document.getElementById('max-magnitude')&&(document.getElementById('max-magnitude').textContent='M'+Math.max(...d.map(e=>e.Magnitude)).toFixed(1));
        document.getElementById('avg-depth')&&(document.getElementById('avg-depth').textContent=Math.round(d.reduce((s,e)=>s+e.Kedalaman,0)/d.length)+' km');
    }
    updatePagination() {
        document.getElementById('showing-from').textContent=Math.min((this.state.currentPage-1)*this.config.itemsPerPage+1,this.state.totalRecords);
        document.getElementById('showing-to').textContent=Math.min(this.state.currentPage*this.config.itemsPerPage,this.state.totalRecords);
        document.getElementById('total-records').textContent=this.state.totalRecords;
        const pb=document.getElementById('prev-page'),nb=document.getElementById('next-page');
        if(pb)pb.disabled=this.state.currentPage<=1;if(nb)nb.disabled=this.state.currentPage>=this.state.totalPages;
    }
    updateTimestamp(){const el=document.getElementById('last-update');if(el)el.textContent=new Date().toLocaleTimeString('id-ID',{timeZone:'Asia/Makassar',hour:'2-digit',minute:'2-digit',second:'2-digit'})+' WITA';}
    showLoading(){document.getElementById('loading-state')?.classList.remove('hidden');document.getElementById('earthquake-table')?.classList.add('hidden');}
    showError() {
        const el=document.getElementById('loading-state');if(!el)return;el.classList.remove('hidden');
        el.innerHTML='<div class="p-8 text-center"><div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4"><span class="material-symbols-outlined text-red-600 text-3xl">cloud_off</span></div>'
        +'<p class="text-lg font-semibold mb-2">Gagal Terhubung ke BMKG</p><p class="text-sm text-slate-500 mb-4">Tidak dapat mengambil data gempa terkini dari server BMKG.</p>'
        +'<button onclick="gempaBumiPage.loadAll(true)" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors inline-flex items-center gap-2"><span class="material-symbols-outlined text-sm">refresh</span> Coba Lagi</button></div>';
    }
    setupAutoRefresh(){setInterval(()=>{if(!this.state.isLoading)this.loadAll();},this.config.updateInterval);setInterval(()=>this.updateTimestamp(),1000);}
    exportCSV() {
        const d=this.state.filteredData.length?this.state.filteredData:this.state.allData;if(!d.length)return;
        const h=['No','Tanggal','Jam','Magnitudo','Kedalaman','Lintang','Bujur','Wilayah','Potensi','Dirasakan'];
        const rows=d.map((e,i)=>[i+1,e.Tanggal,e.Jam,e.Magnitude.toFixed(1),e.KedalamanStr,e.Lintang,e.Bujur,'"'+e.Wilayah.replace(/"/g,'""')+'"','"'+(e.Potensi||'').replace(/"/g,'""')+'"','"'+(e.Dirasakan||'').replace(/"/g,'""')+'"']);
        const csv='\uFEFF'+h.join(',')+'\n'+rows.map(r=>r.join(',')).join('\n');
        const a=document.createElement('a');a.href=URL.createObjectURL(new Blob([csv],{type:'text/csv;charset=utf-8;'}));a.download='gempabumi_'+new Date().toISOString().slice(0,10)+'.csv';a.click();
    }
    magClass(m){return m<3?'bg-green-100 text-green-800':m<4?'bg-yellow-100 text-yellow-800':m<5?'bg-orange-100 text-orange-800':m<6?'bg-red-100 text-red-800':'bg-red-200 text-red-900';}
    fmtWIB(d){try{return new Date(d).toLocaleString('id-ID',{year:'numeric',month:'2-digit',day:'2-digit',hour:'2-digit',minute:'2-digit',timeZone:'Asia/Jakarta'})+' WIB';}catch{return d;}}
    timeAgo(d){try{const m=Math.floor((new Date()-new Date(d))/60000);return m<1?'Baru saja':m<60?m+' menit lalu':m<1440?Math.floor(m/60)+' jam lalu':Math.floor(m/1440)+' hari lalu';}catch{return '';}}
}
let gempaBumiPage;
document.addEventListener('DOMContentLoaded',()=>{gempaBumiPage=new GempaBumiPage();});