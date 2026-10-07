
<div class="form-group"><label class="form-label">Input</label><textarea id="convInput" class="form-textarea" placeholder="Enter text..."></textarea></div>
<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
<button class="btn btn-secondary" onclick="convert('base64encode')">Base64 Encode</button>
<button class="btn btn-secondary" onclick="convert('base64decode')">Base64 Decode</button>
<button class="btn btn-secondary" onclick="convert('upper')">Uppercase</button>
<button class="btn btn-secondary" onclick="convert('lower')">Lowercase</button>
</div>
<button class="btn btn-primary" onclick="convert('auto')">Convert</button>
<div id="convResult" style="margin-top:20px;display:none;" class="card"><pre id="convOut" style="white-space:pre-wrap;word-break:break-all;"></pre></div>
<script>
function convert(mode){
  const input = document.getElementById('convInput').value;
  if(!input){ showToast('Enter input','error'); return; }
  let out='';
  try{
    if(mode==='base64encode') out = btoa(input);
    else if(mode==='base64decode') out = atob(input);
    else if(mode==='upper') out = input.toUpperCase();
    else if(mode==='lower') out = input.toLowerCase();
    else out = btoa(input);
  }catch(e){ out='Error: '+e.message; }
  document.getElementById('convResult').style.display='block';
  document.getElementById('convOut').textContent = out;
  executeTool('video-converter', {input: input.substring(0,100)}, ()=>{});
}
</script>
