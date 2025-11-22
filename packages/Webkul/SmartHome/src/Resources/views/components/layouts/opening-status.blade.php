<div id="opening-status" class="mx-auto w-full" data-dynamic-status>
    <div class="flex flex-col gap-2 rounded-md border border-gray-200 bg-white/80 p-4 shadow-sm backdrop-blur supports-[backdrop-filter]:bg-white/60 md:flex-row md:items-start md:justify-between">
        <div class="flex-1">
            <h2 class="mb-1 text-base font-semibold tracking-tight text-gray-800">Klantenservice</h2>
            <div class="flex items-center gap-2 text-sm">
                <span id="opening-status-text" class="text-gray-500" data-loading="true" aria-live="polite">Status laden...</span>
            </div>
            <div class="mt-1 text-sm text-gray-600">
                <p>Telefoon: <a href="tel:+31858002030" class="text-gray-600 hover:underline">(085) 800 20 30</a></p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function(){
        const tz = 'Europe/Amsterdam';
        const openDays = [1,2,3,4,5];
        const openStart = 10; // 10:00
        const closeHour = 17; // 17:00
        const dayNames = {1:'maandag',2:'dinsdag',3:'woensdag',4:'donderdag',5:'vrijdag',6:'zaterdag',7:'zondag'};
        const textEl = document.getElementById('opening-status-text');

        if(!textEl) return;

        function zonedDate(){
            return new Date(new Date().toLocaleString('en-US',{timeZone: tz}));
        }
        function compute(){
            const now = zonedDate();
            const day = (now.getDay() === 0) ? 7 : now.getDay();
            const h = now.getHours();
            const m = now.getMinutes();
            const isOpenDay = openDays.includes(day);
            const open = isOpenDay && ((h > openStart && h < closeHour) || (h === openStart && m >= 0)) && h < closeHour;
            if(open){
                textEl.innerHTML = 'Open <span class="text-gray-600">- om ' + String(closeHour).padStart(2,'0') + ':00 gesloten</span>';
                textEl.classList.remove('text-red-600');
                textEl.classList.add('text-green-700');
            } else {
                let next = new Date(now.getTime());
                for(let i=0;i<10;i++){
                    const d = (next.getDay()===0)?7:next.getDay();
                    if(openDays.includes(d)){
                        if(next.getHours() < openStart){
                            next.setHours(openStart,0,0,0);
                        }else if(next.getHours() >= closeHour){
                            next.setDate(next.getDate()+1); next.setHours(openStart,0,0,0); continue;
                        }
                        break;
                    } else {
                        next.setDate(next.getDate()+1); next.setHours(openStart,0,0,0);
                    }
                }
                const sameDay = next.toDateString() === now.toDateString();
                if(sameDay && h < openStart){
                    textEl.innerHTML = 'Gesloten <span class="text-gray-600">- om ' + String(openStart).padStart(2,'0') + ':00 open</span>';
                } else if(!sameDay){
                    const nd = (next.getDay()===0)?7:next.getDay();
                    textEl.innerHTML = 'Gesloten <span class="text-gray-600">- ' + dayNames[nd] + ' om ' + String(openStart).padStart(2,'0') + ':00 open</span>';
                } else {
                    const tomorrow = new Date(now.getTime());
                    tomorrow.setDate(tomorrow.getDate()+1);
                    const nd = (tomorrow.getDay()===0)?7:tomorrow.getDay();
                    // after closing
                    textEl.innerHTML = 'Gesloten <span class="text-gray-600">- ' + dayNames[nd] + ' om ' + String(openStart).padStart(2,'0') + ':00 open</span>';
                }
                textEl.classList.remove('text-green-700');
                textEl.classList.add('text-red-600');
            }
        }
        // First paint
        requestAnimationFrame(compute);
        // Align to minute boundary for accuracy
        const nowTs = Date.now();
        setTimeout(function start(){
            compute();
            setInterval(compute, 60000);
        }, 60000 - (nowTs % 60000));
    })();
</script>
@endpush
