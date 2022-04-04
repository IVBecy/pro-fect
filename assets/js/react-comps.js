// Dropdown menu
const RenderDropDown = () => {
  return (
    <div className="dropdown_menu" id="dropdown_settings" style={{ visibility: "hidden", width:"200px" }}>
      <span><a href="../public/profile">Profile</a></span>
      <span><a href="../public/feed">Feed</a></span>
      <span id="new_project">New project</span>
      <hr />
      <span><a href="../public/settings">Settings</a></span>
      <hr />
      <span><a href="../private/logout.php" style={{ color: "red" }}><i className="fas fa-sign-out-alt" style={{ marginRight: "5px" }}></i>Sign out</a></span>
    </div> 
  )
}
// Render the Project post form
const ProjectForm = (props) => {
  return(
    <div className="popup">
      <i className="fas fa-times" style={{ fontSize: "30px" }}></i>
      <h2 id="title">Post a new project</h2>
      <hr/>
      <form method="POST" action="../private/project-gen.php" encType="multipart/form-data">
        <input type="text" name="title" placeholder="Title" required/><br/>
        <span>Characters left: <span id="chars-left">500</span></span><br/>
        <textarea name="desc" placeholder="Description" maxLength="500" required></textarea><br />
        <input type="file" name="preview-img" accept=".png,.jpg,.jpeg" /><br />
        <input type="submit" value="Post" />
        <input type="hidden" name="csrftoken" value={props.csrf__token}/>
      </form>
    </div>
  )
};
// When you click on the ellipses, you get options regarding your project
const ProjectSettings = () => {
  return(
    <div className="dropdown_menu" id="project-dropdown" style={{visibility:"visible"}}>
      <span id="edit-post">Edit</span>
      <span id="delete-post">Delete</span>
    </div> 
  )
};
// Edit a post
const RenderPostEdit = (props) =>{
  return(
    <div className="popup">
      <i className="fas fa-times" style={{ fontSize: "30px" }}></i>
      <h1>Edit your post</h1>
      <hr />
      <form method="POST" onSubmit={onPostEdit} action="../private/edit-post.php">
        <input type="text" name="title" placeholder="Edit title" /><br />
        <span>Characters left: <span id="chars-left">500</span></span><br />
        <textarea name="desc" placeholder="Edit description" maxLength="500"></textarea><br />
        <input type="submit" name="send-edited-post" value="Edit Post" />
        <input type="hidden" name="csrftoken" value={props.csrf__token}/>
      </form>
    </div>
  )
};
// Delete a post
const DeletePost = (props) => {
  return (
    <div className="popup" id="delete">
      <i className="fas fa-times" style={{ fontSize: "30px" }}></i>
      <form method="POST" action="../private/delete-post.php">
        <h4>Are you sure that you want to delete your project?</h4>
        <input type="submit" value="Delete" style={{backgroundColor:"red",color:"white"}}/>
        <input type="hidden" name="csrftoken" value={props.csrf__token}/>
      </form>
    </div>
  )
};
// Deleting account
const DeleteAccount = (props) => {
  return (
    <div className="popup" id="delete">
    <i className="fas fa-times" style={{ fontSize: "30px" }}></i>
      <form method="POST" action="../private/delete-account.php">
        <h4>Are you sure that you want to delete your account?</h4>
        <p style={{color:"red"}}>After this there is NO turning back!</p>
        <input type="submit" value="Delete" style={{ backgroundColor: "red", color: "white" }} />
        <input type="hidden" name="csrftoken" value={props.csrf__token} />
      </form>
    </div>
  )
};
// Footer
const MainFooter = () => {
  return(
    <footer>
      <div className="footer-links">
        <a href="../public/tos">Terms of Service</a>
        <a href="../public/privacy">Privacy policy</a>
        <a href="../public/cookie">Cookie policy</a>
      </div>
    </footer>
  )
};
// For seeing people who starred a post
const StarInterface = (props) => {
  return(
    <div className="popup star-interface">
      <i className="fas fa-times" style={{fontSize:"25px"}}></i>
      <h1>Star Gazers</h1>
      <hr/>
      <div>{props.starGazers}</div>
    </div>
  )
};