(function () {
    const iframeName = 'wc_iframe';
    const iframeUrl = '/iframe';

    const styleTag = document.createElement('style');
    styleTag.innerHTML = String.raw`
    .modal{display:none;position:fixed;z-index:1;padding-top:100px;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgba(0,0,0,.4)}.modal-content{position:relative;background-color:#fefefe;margin:auto;padding:0;border:1px solid #888;width:80%;box-shadow:0 4px 8px 0 rgba(0,0,0,.2),0 6px 20px 0 rgba(0,0,0,.19);-webkit-animation-name:animatetop;-webkit-animation-duration:.4s;animation-name:animatetop;animation-duration:.4s}@-webkit-keyframes animatetop{from{top:-300px;opacity:0}to{top:0;opacity:1}}@keyframes animatetop{from{top:-300px;opacity:0}to{top:0;opacity:1}}.close{color:#000;float:right;font-size:28px;font-weight:700;margin-top:-10px}.close:focus,.close:hover{color:#000;text-decoration:none;cursor:pointer}.modal-footer,.modal-header{padding:10px;background-color:#ececec;color:#000;min-height:40px}.modal-body{padding:10px;min-height:100px}
    #wcButton{position:fixed;width:60px;height:60px;bottom:40px;right:40px;background-color:#0C9;color:#FFF;border-radius:50px;text-align:center;box-shadow: 2px 2px 3px #999;cursor:pointer}#wcButton i{margin-top:22px}`;
    document.head.appendChild(styleTag);

    const buttonTag = document.createElement('div');
    buttonTag.id = 'wcButton';
    buttonTag.innerHTML = String.raw`<i class="fa fa-calendar"></i>`;
    document.body.appendChild(buttonTag);
    const modalTag = document.createElement('div');
    modalTag.innerHTML = String.raw`
    <div id="wcModal" class="modal">
      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-header">
          <span class="close">&times;</span>
          <h2>Selected Number: <span id="wc_number"></span></h2>
        </div>
        <div class="modal-body" id="wc_modal_body">
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
    `;
    document.body.appendChild(modalTag);
    document.getElementById("wc_number").innerHTML = window.when.random || '';

    const wcModalBody = document.getElementById("wc_modal_body");
    const formTag = document.createElement('form');
    formTag.method = "POST";
    formTag.action = iframeUrl;
    formTag.target = iframeName;

    const formInput = document.createElement("input");
    formInput.value = window.when.random || '';
    formInput.name = "number";
    formInput.type = "hidden";
    formTag.appendChild(formInput);
    const formInputToken = document.createElement("input");
    formInputToken.value = window.Laravel.csrfToken || '';
    formInputToken.name = "_token";
    formInputToken.type = "hidden";
    formTag.appendChild(formInputToken);

    const iframeTag = document.createElement('iframe');
    iframeTag.style.display = "none";
    iframeTag.name = iframeName;
    iframeTag.style.width = "100%";

    const formSubmitButton = document.createElement("button");
    formSubmitButton.type = 'submit';
    formSubmitButton.innerText = 'Submit';
    formSubmitButton.className = 'inline-flex items-center px-4 py-2 bg-brand-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-700 active:bg-brand-900 focus:outline-none focus:border-brand-900 focus:ring focus:ring-brand-300 disabled:opacity-25 transition ml-4';

    wcModalBody.appendChild(formTag);
    wcModalBody.appendChild(iframeTag);
    wcModalBody.appendChild(formSubmitButton);

    const modal = document.getElementById("wcModal");
    const btn = document.getElementById("wcButton");
    const span = document.getElementsByClassName("close")[0];

    formSubmitButton.onclick = function () {
        formTag.submit();
        formTag.style.display = "none";
        formSubmitButton.style.display = "none";
        iframeTag.style.display = "block";
    }

    btn.onclick = function () {
        modal.style.display = "block";
        btn.style.display = "none";

    }
    span.onclick = function () {
        modal.style.display = "none";
        btn.style.display = "block";
    }
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
            btn.style.display = "block";
        }
    }
})();
