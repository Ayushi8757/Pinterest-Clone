<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../components/login.php');
    exit;
}

include "../config/database.php";
$user_id = $_SESSION['user_id'];

$success    = false;
$error      = '';
$new_pin_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']            ?? '');
    $description = trim($_POST['description']      ?? '');
    $dest_link   = trim($_POST['destination_link'] ?? '');
    $board_id    = intval($_POST['board_id']        ?? 0);
    $category    = trim($_POST['category']          ?? '');
    $imageUrl    = '';

    if (empty($title)) {
        $error = 'Title is required.';
    } elseif ($board_id <= 0) {
        $error = 'Please select a board.';
    } else {

        // ── Handle file upload ──
        if (isset($_FILES['pin_image']) && $_FILES['pin_image']['error'] !== UPLOAD_ERR_NO_FILE) {

            $fileError = $_FILES['pin_image']['error'];

            if ($fileError !== UPLOAD_ERR_OK) {
                $phpErrors = [
                    1 => 'File too large (increase upload_max_filesize in php.ini).',
                    2 => 'File too large (form limit).',
                    3 => 'File only partially uploaded.',
                    4 => 'No file was uploaded.',
                    6 => 'Missing temp folder.',
                    7 => 'Failed to write to disk.',
                    8 => 'Upload stopped by extension.',
                ];
                $error = $phpErrors[$fileError] ?? 'PHP upload error: ' . $fileError;
            } else {
                $projectRoot = realpath(__DIR__ . '/../');
                $uploadDir   = $projectRoot . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'pins' . DIRECTORY_SEPARATOR;

                if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

                if (!is_writable($uploadDir)) {
                    $error = 'Upload folder not writable: ' . $uploadDir;
                } else {
                    $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
                    $ftype   = mime_content_type($_FILES['pin_image']['tmp_name']);

                    if (!in_array($ftype, $allowed)) {
                        $error = 'Only JPG, PNG, GIF, WEBP allowed.';
                    } elseif ($_FILES['pin_image']['size'] > 20 * 1024 * 1024) {
                        $error = 'Image too large (max 20MB).';
                    } else {
                        $ext      = strtolower(pathinfo($_FILES['pin_image']['name'], PATHINFO_EXTENSION));
                        $fileName = 'pin_' . $user_id . '_' . time() . '.' . $ext;
                        $destPath = $uploadDir . $fileName;

                        if (move_uploaded_file($_FILES['pin_image']['tmp_name'], $destPath)) {
                            $imageUrl = 'uploads/pins/' . $fileName;
                        } else {
                            $error = 'move_uploaded_file() failed. Dir: ' . $uploadDir;
                        }
                    }
                }
            }

        } elseif (!empty($_POST['image_url'])) {
            $imageUrl = trim($_POST['image_url']);
        }

        // ── Save to DB ──
        if (empty($error)) {
            $titleSafe = mysqli_real_escape_string($conn, $title);
            $descSafe  = mysqli_real_escape_string($conn, $description);
            $linkSafe  = mysqli_real_escape_string($conn, $dest_link);
            $catSafe   = mysqli_real_escape_string($conn, $category);
            $imgSafe   = mysqli_real_escape_string($conn, $imageUrl);

            $insertPin = mysqli_query($conn, "
                INSERT INTO pins (user_id, title, description, image, destination_link, category, created_at)
                VALUES ('$user_id', '$titleSafe', '$descSafe', '$imgSafe', '$linkSafe', '$catSafe', NOW())
            ");

            if ($insertPin) {
                $new_pin_id = mysqli_insert_id($conn);
                mysqli_query($conn, "INSERT INTO board_pins (board_id, pin_id) VALUES ('$board_id', '$new_pin_id')");
                $success = true;
            } else {
                $error = 'Could not save pin: ' . mysqli_error($conn);
            }
        }
    }
}

// Fetch user boards
$boardsResult = mysqli_query($conn, "SELECT board_id, board_name FROM boards WHERE user_id='$user_id' ORDER BY created_at DESC");
$userBoards = [];
if ($boardsResult) while ($b = mysqli_fetch_assoc($boardsResult)) $userBoards[] = $b;

// Nav vars
$profileImg     = $_SESSION['profile_image'] ?? '';
$profileName    = htmlspecialchars($_SESSION['user_name'] ?? 'User');
$firstLetter    = strtoupper(mb_substr($profileName, 0, 1));
$profileImgDisp = $profileImg ? '../' . $profileImg : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pinterest – Create Pin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<?php require_once __DIR__ . "/app_shell.php"; ?>
  <link rel="stylesheet" href="../css/create-pin.css" />
  <link rel="stylesheet" href="../css/app-shell.css" />
</head>
<body>

<?php render_app_shell([
    'scope' => 'components',
    'active' => 'create',
]); ?>

<div class="main">
  <div style="max-width:960px;margin:0 auto 18px;">
    <a href="../home.php" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none;color:#555;font-weight:600;">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>
  <div class="create-wrap">

    <!-- Upload Side -->
    <div class="upload-side">
      <div class="drop-zone" id="dropZone"
           ondragover="onDrag(event)" ondragleave="offDrag()" ondrop="onDrop(event)">
        <img id="previewImg" src="" alt=""/>
        <div id="dzPlaceholder" style="display:flex;flex-direction:column;align-items:center;padding:20px;text-align:center;">
          <div class="dz-icon"><i class="bi bi-cloud-upload"></i></div>
          <div class="dz-hint"><b>Click to upload</b> or drag & drop<br/>PNG, JPG, GIF, WEBP · max 20MB</div>
        </div>
        <input type="file" id="fileInput" accept="image/*" onchange="previewFile(this)"/>
      </div>
      <div class="img-error" id="imgError">
        <i class="bi bi-exclamation-circle me-1"></i><span id="imgErrMsg">Please upload an image.</span>
      </div>
      <div class="url-wrap">
        <label>Or paste image URL</label>
        <input type="url" id="urlInput" placeholder="https://example.com/image.jpg" oninput="loadUrl(this.value)"/>
      </div>
    </div>

    <!-- Form Side -->
    <div class="form-side">
      <h2>Create a Pin</h2>

      <form id="pinForm" method="POST" action="create-pin.php" enctype="multipart/form-data" novalidate>

        <!-- Hidden real file input inside form -->
        <input type="file" id="formFileInput" name="pin_image" accept="image/*" style="position:absolute;left:-9999px;opacity:0;"/>
        <input type="hidden" name="image_url" id="imageUrlHidden" value=""/>

        <div class="fg">
          <label>Title <span style="color:var(--pin-red)">*</span></label>
          <input type="text" class="fc" id="pinTitle" name="title" placeholder="Add a title" maxlength="100" oninput="countChars(this,'titleCount',100)"/>
          <div class="field-err">Title is required (min 3 characters).</div>
          <div class="char-count" id="titleCount">0 / 100</div>
        </div>

        <div class="fg">
          <label>Description</label>
          <textarea class="fc" id="pinDesc" name="description" rows="3" placeholder="Tell everyone what your Pin is about" maxlength="500" oninput="countChars(this,'descCount',500)"></textarea>
          <div class="char-count" id="descCount">0 / 500</div>
        </div>

        <div class="fg">
          <label>Link <span style="font-weight:400;color:#767676;font-size:.82rem;">(optional)</span></label>
          <input type="url" class="fc" id="pinLink" name="destination_link" placeholder="https://source-website.com"/>
          <div class="field-err">Please enter a valid URL.</div>
        </div>

        <div class="row2">
          <div class="fg">
            <label>Board <span style="color:var(--pin-red)">*</span></label>
            <select class="fc" id="pinBoard" name="board_id">
              <option value="">Select a board…</option>
              <?php foreach ($userBoards as $b): ?>
                <option value="<?= $b['board_id'] ?>"><?= htmlspecialchars($b['board_name']) ?></option>
              <?php endforeach; ?>
              <?php if (empty($userBoards)): ?>
                <option disabled>No boards yet — create one first</option>
              <?php endif; ?>
            </select>
            <div class="field-err">Please select a board.</div>
          </div>
          <div class="fg">
            <label>Category</label>
            <select class="fc" id="pinCategory" name="category">
              <option value="">Choose category…</option>
              <option value="travel">Travel</option>
              <option value="food">Food</option>
              <option value="fashion">Fashion</option>
              <option value="home">Home</option>
              <option value="art">Art</option>
              <option value="fitness">Fitness</option>
              <option value="tech">Technology</option>
              <option value="diy">DIY</option>
              <option value="nature">Nature</option>
              <option value="beauty">Beauty</option>
              <option value="animals">Animals</option>
            </select>
          </div>
        </div>

        <div class="fg">
          <label>Tags <span style="font-weight:400;color:#767676;font-size:.82rem;">(Enter dabao, max 10)</span></label>
          <input type="text" class="fc" id="tagInput" placeholder="e.g. minimalist, home decor" onkeydown="addTag(event)"/>
          <div class="tag-wrap" id="tagWrap"></div>
          <div id="tagErr" style="font-size:.8rem;color:#dc3545;margin-top:4px;display:none;"></div>
        </div>

        <div class="fg">
          <label>Visibility</label>
          <select class="fc" name="visibility">
            <option value="public"> Public</option>
            <option value="secret"> Only me (Secret)</option>
          </select>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;">
          <button type="button" class="btn-draft" onclick="saveDraft()"><i class="bi bi-floppy me-1"></i>Save draft</button>
          <button type="submit" class="btn-pin" id="publishBtn"><i class="bi bi-send-fill me-1"></i>Publish</button>
        </div>

        <?php if ($success): ?>
          <div class="alert-ok">
             <strong>Your pin is live!</strong> &nbsp;
            <a href="../home.php" style="color:var(--pin-red);font-weight:600;text-decoration:none;">View on feed →</a>
            &nbsp;|&nbsp;
            <a href="profile.php" style="color:var(--pin-red);font-weight:600;text-decoration:none;">Your profile →</a>
          </div>
        <?php elseif (!empty($error)): ?>
          <div class="alert-err"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

      </form>
    </div>
  </div>
</div>

<div class="toast-wrap">
  <div id="draftToast" class="toast align-items-center text-bg-dark border-0 rounded-pill px-3" role="alert">
    <div class="d-flex"><div class="toast-body fw-semibold"> Draft saved!</div></div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let tags = [], hasImage = false, selectedFile = null;

  function previewFile(input) {
    const file = input.files[0];
    if (!file) return;
    if (!['image/jpeg','image/png','image/gif','image/webp'].includes(file.type)) { showImgError('Only JPG, PNG, GIF, WEBP allowed.'); return; }
    if (file.size > 20*1024*1024) { showImgError('File too large (max 20MB).'); return; }
    selectedFile = file;
    showPreview(URL.createObjectURL(file));
  }

  function loadUrl(val) {
    if (/^https?:\/\/.+\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i.test(val)) {
      selectedFile = null;
      showPreview(val);
    }
  }

  function showPreview(url) {
    const img = document.getElementById('previewImg');
    img.onload = () => {
      document.getElementById('dropZone').classList.remove('dz-invalid');
      document.getElementById('dropZone').classList.add('dz-valid');
      document.getElementById('imgError').classList.remove('show');
      document.getElementById('dzPlaceholder').style.display = 'none';
      hasImage = true;
    };
    img.onerror = () => showImgError('Could not load image.');
    img.src = url;
    img.style.display = 'block';
  }

  function showImgError(msg) {
    hasImage = false; selectedFile = null;
    document.getElementById('dropZone').classList.add('dz-invalid');
    document.getElementById('dropZone').classList.remove('dz-valid');
    document.getElementById('imgErrMsg').textContent = msg;
    document.getElementById('imgError').classList.add('show');
    document.getElementById('previewImg').style.display = 'none';
    document.getElementById('dzPlaceholder').style.display = 'flex';
  }

  function onDrag(e) { e.preventDefault(); document.getElementById('dropZone').classList.add('drag'); }
  function offDrag() { document.getElementById('dropZone').classList.remove('drag'); }
  function onDrop(e) {
    e.preventDefault(); offDrag();
    const file = e.dataTransfer.files[0];
    if (!file) return;
    selectedFile = file;
    previewFile({files:[file]});
  }

  function countChars(el,id,max) {
    const len=el.value.length, c=document.getElementById(id);
    c.textContent=len+' / '+max;
    c.className='char-count'+(len>=max?' over':len>max*.9?' warn':'');
  }

  function addTag(e) {
    if(e.key!=='Enter') return; e.preventDefault();
    const input=document.getElementById('tagInput'), val=input.value.trim(), err=document.getElementById('tagErr');
    if(!val) return;
    if(tags.length>=10){err.textContent='Max 10 tags.';err.style.display='block';return;}
    if(tags.includes(val.toLowerCase())){err.textContent='Tag already added.';err.style.display='block';return;}
    err.style.display='none'; tags.push(val.toLowerCase()); input.value=''; renderTags();
  }
  function removeTag(i){tags.splice(i,1);renderTags();}
  function renderTags(){
    document.getElementById('tagWrap').innerHTML=tags.map((t,i)=>
      `<span class="tag">#${t}<button type="button" onclick="removeTag(${i})">×</button></span>`
    ).join('');
  }

  function markValid(el){el.classList.remove('is-invalid');el.classList.add('is-valid');}
  function markInvalid(el){el.classList.remove('is-valid');el.classList.add('is-invalid');}
  function validateTitle(){const el=document.getElementById('pinTitle');if(el.value.trim().length<3){markInvalid(el);return false;}markValid(el);return true;}
  function validateBoard(){const el=document.getElementById('pinBoard');if(!el.value){markInvalid(el);return false;}markValid(el);return true;}
  function validateLink(){const el=document.getElementById('pinLink');if(!el.value){markValid(el);return true;}try{new URL(el.value);markValid(el);return true;}catch{markInvalid(el);return false;}}

  document.getElementById('pinTitle').addEventListener('blur', validateTitle);
  document.getElementById('pinBoard').addEventListener('change', validateBoard);
  document.getElementById('pinLink').addEventListener('blur', validateLink);

  document.getElementById('pinForm').addEventListener('submit', function(e) {
    const titleOk=validateTitle(), boardOk=validateBoard(), linkOk=validateLink();
    if(!hasImage) showImgError('Please upload an image for your pin.');
    if(!titleOk||!boardOk||!linkOk||!hasImage){
      e.preventDefault();
      const firstErr=document.querySelector('.is-invalid,.dz-invalid');
      if(firstErr) firstErr.scrollIntoView({behavior:'smooth',block:'center'});
      return;
    }

    // Copy file from drop-zone input into form's hidden file input
    if(selectedFile) {
      try {
        const dt=new DataTransfer();
        dt.items.add(selectedFile);
        document.getElementById('formFileInput').files=dt.files;
      } catch(err) {
        // DataTransfer not supported — fallback: directly assign fileInput files
        document.getElementById('formFileInput').files=document.getElementById('fileInput').files;
      }
    }

    // URL mode
    document.getElementById('imageUrlHidden').value = selectedFile ? '' : (document.getElementById('urlInput').value||'');

    // Show spinner — then let browser submit NATIVELY
    const btn=document.getElementById('publishBtn');
    btn.disabled=true;
    btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Publishing…';
  });

  function saveDraft(){new bootstrap.Toast(document.getElementById('draftToast'),{delay:2000}).show();}
</script>
</body>
</html>


