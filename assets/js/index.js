// Get CSRF token
var csrfToken = ("; "+document.cookie).split("; CSRF-Token=").pop().split(";").shift();

// Max chars in a post
const maxCharPost = 500;

// Character counter function
const charCounter = (max,curChar) =>{
  window.maxChar = max - curChar;
  return maxChar;
};

// Dropdown settings menu
$(document).ready(() => {
  if (document.getElementById("menu")){
    ReactDOM.render(<RenderDropDown/>, document.getElementById("menu"))
    setTimeout(() => {
      var name = document.getElementById("dropdown-img");
      var elem = document.getElementById("dropdown_settings");
      if (name) {
        name.onclick = () => {
          if (elem.style.visibility == "hidden") {
            elem.style.visibility = "visible";
          } else {
            elem.style.visibility = "hidden";
          };
        };
      };
    }, 100);
  };
});

// Spawning footer
$(document).ready(() => {
  if (document.getElementById("footer")){
    ReactDOM.render(<MainFooter/>,document.getElementById("footer"))
  };
});


// when clicking the "new project" span
$(document).ready(() => {
  var trigger = document.getElementById("new_project");
  var overlay = document.getElementById("overlay");
  if (trigger) {
    trigger.onclick = () => {
      if (overlay) {
        overlay.style.display = "block";
      }
      setTimeout(() => {ReactDOM.render(<ProjectForm csrf__token={csrfToken} />, overlay)}, 100)
      setTimeout(() => {
        // Character counter
        document.getElementsByName("desc")[0].onkeyup = () =>{
          charCounter(maxCharPost,document.getElementsByName("desc")[0].value.length)
          document.getElementById("chars-left").textContent = window.maxChar;
        }
        // When clicking the "x" in the overlay
        var x = document.getElementsByClassName("fas fa-times")[0];
        if (x && overlay.style.display == "block") {
          x.onclick = () => {
            overlay.style.display = "none";
          };
        };
      },200)
    };
  };
});


// When we click the edit post button
const onPostEdit = () => {
  var newTitle = document.getElementsByName("title")[0].value;
  var newDesc = document.getElementsByName("desc")[0].value;
  var newProj = {
    "newTitle": newTitle,
    "newDesc": newDesc
  };
  document.cookie = `editedPost=${JSON.stringify(newProj)}; path=/ `
}
// Project edits
$(document).ready(() => {
  var overlay = document.getElementById("overlay");
  if (document.querySelectorAll(".fas.fa-ellipsis-h")[0]){
    var elips = document.querySelectorAll(".fas.fa-ellipsis-h");
    for (var i in elips){
      if (typeof elips[i] != "object"){}
      else{
        elips[i].onclick = (e) => {
          var elem = document.getElementById("project-dropdown");
          if (elem) {
            ReactDOM.unmountComponentAtNode(document.getElementById("project-edits-menu"))
          } else {
            ReactDOM.render(<ProjectSettings />, document.getElementById("project-edits-menu"))
            //editing posts
            document.getElementById("edit-post").onclick = () => {
              var title = e.target.parentNode.id;
              var projectDesc = e.target.parentNode.getElementsByClassName("project-desc")[0].innerHTML;
              overlay.style.display = "block";
              ReactDOM.render(<RenderPostEdit csrf__token={csrfToken}  />,overlay)
              //set values to the input fields
              setTimeout(() => {
                document.getElementsByName("title")[0].value = title;
                document.getElementsByName("desc")[0].value = projectDesc;
                var oldTitle = document.getElementsByName("title")[0].value;
                var oldDesc = document.getElementsByName("desc")[0].value;
                var oldArray = {
                  "oldTitle": oldTitle,
                  "oldDesc": oldDesc
                };
                document.cookie = `oldPost=${JSON.stringify(oldArray)}; path=/ `
                document.getElementById("chars-left").textContent = charCounter(maxCharPost,oldDesc.length);
                // Character counter
                document.getElementsByName("desc")[0].onkeyup = () =>{
                  charCounter(maxCharPost,document.getElementsByName("desc")[0].value.length)
                  document.getElementById("chars-left").textContent = window.maxChar;
                }
              },100)
              var x = document.getElementsByClassName("fas fa-times")[0];
              if (x && overlay.style.display == "block") {
                x.onclick = () => {
                  overlay.style.display = "none";
                };
              };
            };
            // Post deletion
            document.getElementById("delete-post").onclick = () => {
              overlay.style.display = "block";
              ReactDOM.render(<DeletePost csrf__token={csrfToken} />, overlay)
              var x = document.getElementsByClassName("fas fa-times")[0];
              //set cookie for php top be able to access the item in the array
              document.cookie = `ToBeDeleted=${e.target.parentNode.id} ; path=/ `;
              if (x && overlay.style.display == "block") {
                x.onclick = () => {
                  overlay.style.display = "none";
                };
              };
            }
          };
        };
      };
    };
  };
});
// Delete account
$(document).ready(() => {
  var overlay = document.getElementById("overlay");
  var deleteAccBtn = document.getElementById("delete-acc-btn");
  if (deleteAccBtn){
    deleteAccBtn.onclick = () =>{
      overlay.style.display = "block";
      ReactDOM.render(<DeleteAccount csrf__token={csrfToken} />, overlay)
      setTimeout(() => {
        var x = document.getElementsByClassName("fas fa-times")[0];
        if (x && overlay.style.display == "block") {
          x.onclick = () => {
            overlay.style.display = "none";
          };
        };
      },100)
    }
  }
});