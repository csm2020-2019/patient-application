# Frontend and API Entry Point

Ideally, the frontend and the backend API application would be on completely separate servers, with entirely different 
public_html folders and repositories. Due to time and resource constraints, they will have to share.

The only file belonging to the backend API is its entry-point, **api.php** that must be Internet-accessible. It does not
directly interact with any other file in this directory.
