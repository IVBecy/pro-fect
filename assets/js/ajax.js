// function for username lookup
const nameLookup = (str) => {
  var nameDiv = document.getElementById("name-guess");
  nameDiv.style.visibility = "visible";
  document.body.onclick = () => { nameDiv.style.visibility = "hidden";}
  str.toLowerCase()
  if (str.length == 0) {
    nameDiv.style.visibility = "hidden"
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = () => {
      nameDiv.innerHTML = xmlhttp.responseText;
    };
    xmlhttp.open("GET", `../private/name-lookup.php?str=${str}`, true);
    xmlhttp.send();
  }
}
// function for starring posts
const Starring = (title,id) => {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = () => {
    document.getElementById(`${title}${id}star`).innerHTML = xmlhttp.responseText;
  };
  xmlhttp.open("GET", `../private/star.php?id=${id}&title=${title}`, true);
  xmlhttp.send();
}