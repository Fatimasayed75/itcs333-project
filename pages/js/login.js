const signUpButton = document.getElementById('signUp');
const haveAccount = document.getElementById('haveAccount');
console.log(haveAccount);
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

signUpButton.addEventListener('click', () => {
	container.classList.add("right-panel-active");
});

signInButton.addEventListener('click', () => {
	container.classList.remove("right-panel-active");
});

haveAccount.addEventListener('click', () => {
	container.classList.add("right-panel-active");
});