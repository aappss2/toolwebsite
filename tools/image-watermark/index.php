
<div class="form-group"><label class="form-label">Upload Image</label><input type="file" id="imgFile" class="form-input" accept="image/*"></div>
<div style="margin:16px 0;"><img id="imgPreview" style="max-width:100%;display:none;border-radius:var(--radius);"></div>
<button class="btn btn-primary" onclick="processImage()">Process Image</button>
<div id="imgResult" style="margin-top:20px;display:none;" class="card"></div>
<script>
const imgInput = document.getElementById('imgFile');
const preview = document.getElementById('imgPreview');
imgInput.addEventListener('change', ()=>{
  const file = imgInput.files[0];
  if(!file) return;
  preview.src = URL.createObjectURL(file);
  preview.style.display='block';
});
function processImage(){
  if(!imgInput.files[0]){ showToast('Select image','error'); return; }
  document.getElementById('imgResult').style.display='block';
  document.getElementById('imgResult').innerHTML = '<h3>IMAGE WATERMARK — Processed</h3><p>File: '+imgInput.files[0].name+'</p>';
  executeTool('image-watermark', {input: imgInput.files[0].name}, ()=>{});
}
</script>
