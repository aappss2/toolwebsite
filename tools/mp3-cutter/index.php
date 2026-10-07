
<div class="form-group"><label class="form-label">Upload Media</label><input type="file" id="mediaFile" class="form-input" accept="audio/*,video/*"></div>
<button class="btn btn-primary" onclick="processMedia()">Process</button>
<div id="mediaResult" style="margin-top:20px;display:none;" class="card"></div>
<script>
function processMedia(){
  const file = document.getElementById('mediaFile').files[0];
  if(!file){ showToast('Select file','error'); return; }
  document.getElementById('mediaResult').style.display='block';
  document.getElementById('mediaResult').innerHTML = '<h3>MP3 Cutter</h3><p>File: '+file.name+'</p>';
  executeTool('mp3-cutter', {input: file.name}, ()=>{});
}
</script>
