
<div class="alert alert-info">PDF tool: Compress PDF. Upload files securely.</div>
<div class="form-group"><label class="form-label">Upload PDF Files</label><input type="file" id="pdfFiles" class="form-input" multiple accept=".pdf"></div>
<div id="pdfDrop" style="border:2px dashed var(--border);border-radius:var(--radius);padding:40px;text-align:center;margin:16px 0;">Drag & drop files here</div>
<button class="btn btn-primary" onclick="processPDF()">Process PDF</button>
<div id="pdfResult" style="margin-top:20px;display:none;" class="card"></div>
<script>
const drop = document.getElementById('pdfDrop');
const input = document.getElementById('pdfFiles');
drop.addEventListener('dragover', e=>{ e.preventDefault(); drop.style.borderColor='var(--primary)'; });
drop.addEventListener('dragleave', ()=>{ drop.style.borderColor='var(--border)'; });
drop.addEventListener('drop', e=>{ e.preventDefault(); input.files = e.dataTransfer.files; drop.textContent = e.dataTransfer.files.length+' files selected'; });
input.addEventListener('change', ()=>{ drop.textContent = input.files.length+' files selected'; });
function processPDF(){
  if(!input.files.length){ showToast('Select files','error'); return; }
  document.getElementById('pdfResult').style.display='block';
  document.getElementById('pdfResult').innerHTML = '<h3>Processing '+input.files.length+' file(s) — Compress PDF</h3><p>Demo mode: would use pdf-lib.js server-side with validation.</p>';
  executeTool('compress-pdf', {input: input.files.length+' files'}, ()=>{});
}
</script>
