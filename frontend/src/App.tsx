import { useState } from 'react';
import { useAuth } from './context/AuthContext.tsx';
import Register from './pages/Register.tsx';
import Login from './pages/Login.tsx';
import Profile from './pages/Profile.tsx';
//import './App.css'

export default function App() {
  const { user, loading } = useAuth();
  const [view, setView] = useState<'login' | 'register'>('login');

  if (loading) return <p>Betöltés...</p>;

  if (user) return <Profile />;

  return (
    <div>
      <button onClick={() => setView('login')}>Login</button>
      <button onClick={() => setView('register')}>Register</button>
      {view === 'login' ? <Login /> : <Register />}
    </div>
  );

}