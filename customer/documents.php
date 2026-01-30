<?php
include 'db/config.php';
include 'header.php';
include 'topbar.php';
include 'sidebar.php';
?>

<style>
.doc-card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:18px;
    transition:.3s;
}
.doc-card:hover{
    box-shadow:0 10px 25px rgba(0,0,0,.05);
}
.badge{
    padding:6px 12px;
    border-radius:999px;
    font-size:.75rem;
    font-weight:700;
}
.pending{background:#fef3c7;color:#92400e}
.approved{background:#dcfce7;color:#166534}
.rejected{background:#fee2e2;color:#991b1b}
</style>

<div class="content-page">
<div class="content">
<div class="container-fluid">

<h3 class="fw-bold mb-4">My Uploaded Documents</h3>

<div class="row" id="docBox"></div>

</div>
</div>
</div>

<script>
async function loadDocuments(){
    const res = await fetch('db/get_documents.php?action=fetch');
    const docs = await res.json();
    const box = document.getElementById('docBox');
    box.innerHTML = '';

    if(docs.length === 0){
        box.innerHTML = `<div class="col-12 text-muted">No documents uploaded yet.</div>`;
        return;
    }

    docs.forEach(d => {
        let badgeClass = d.status.toLowerCase();
        let deleteBtn = '';

        if(d.status === 'rejected'){
            deleteBtn = `
                <button onclick="deleteDoc(${d.id})"
                class="btn btn-sm btn-outline-danger mt-2">
                    Delete
                </button>`;
        }

        box.innerHTML += `
        <div class="col-md-6 mb-4">
            <div class="doc-card">
                <h6 class="fw-bold mb-1">${d.doc_name}</h6>
                <p class="text-muted mb-1 small">
                    Loan: <b>${d.service_name}</b> (ID #${d.loan_id})
                </p>
                <span class="badge ${badgeClass}">${d.status}</span>

                ${d.status === 'rejected' && d.rejection_reason 
                    ? `<p class="text-danger small mt-2">
                        Reason: ${d.rejection_reason}
                       </p>` : ''}

                <div class="mt-3">
                    <a href="../${d.doc_path}" target="_blank"
                       class="btn btn-sm btn-outline-primary">
                       View Document
                    </a>
                    ${deleteBtn}
                </div>
            </div>
        </div>`;
    });
}

async function deleteDoc(id){
    if(!confirm('Delete this rejected document?')) return;

    const form = new FormData();
    form.append('doc_id', id);

    const res = await fetch('db/get_documents.php?action=delete',{
        method:'POST',
        body:form
    });

    const r = await res.json();
    if(r.success){
        loadDocuments();
    }
}

loadDocuments();
</script>

<?php include 'footer.php'; ?>
