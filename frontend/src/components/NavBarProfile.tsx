import { Menu, MenuButton, MenuItem, MenuItems, Transition } from '@headlessui/react';
import { Fragment } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function NavBarProfile() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const initials = user?.name
    ?.split(' ')
    .map((n) => n[0])
    .join('')
    .slice(0, 2)
    .toUpperCase() ?? '?';

  return (
    <Menu as="div" className="relative">
      <MenuButton className="w-[30px] h-[30px] rounded-full bg-[#eceae5] border border-[#e3e0da] flex items-center justify-center text-[11px] font-semibold text-secondary">
        {initials}
      </MenuButton>

      <Transition
        as={Fragment}
        enter="transition ease-out duration-100"
        enterFrom="transform opacity-0 scale-95"
        enterTo="transform opacity-100 scale-100"
        leave="transition ease-in duration-75"
        leaveFrom="transform opacity-100 scale-100"
        leaveTo="transform opacity-0 scale-95"
      >
        <MenuItems className="absolute right-0 mt-2 w-48 origin-top-right rounded-xl bg-white border border-[#e3e0da] shadow-lg focus:outline-none overflow-hidden z-50">
          <div className="px-4 py-3 border-b border-[#e3e0da]">
            <p className="text-sm font-semibold text-ink truncate">{user?.name}</p>
            <p className="text-xs text-[#8b877f] truncate">{user?.email}</p>
          </div>

          <div className="py-1">
            <MenuItem>
              {({ active }) => (
                <button
                  onClick={() => navigate('/rentals')}
                  className={`w-full text-left px-4 py-2.5 text-sm ${active ? 'bg-bg text-ink' : 'text-secondary'}`}
                >
                  Bérléseim
                </button>
              )}
            </MenuItem>
            <MenuItem>
              {({ active }) => (
                <button
                  onClick={() => navigate('/profile')}
                  className={`w-full text-left px-4 py-2.5 text-sm ${active ? 'bg-bg text-ink' : 'text-secondary'}`}
                >
                  Profilom
                </button>
              )}
            </MenuItem>
          </div>

          <div className="py-1 border-t border-[#e3e0da]">
            <MenuItem>
              {({ active }) => (
                <button
                  onClick={handleLogout}
                  className={`w-full text-left px-4 py-2.5 text-sm ${active ? 'bg-bg text-danger' : 'text-danger'}`}
                >
                  Kijelentkezés
                </button>
              )}
            </MenuItem>
          </div>
        </MenuItems>
      </Transition>
    </Menu>
  );
}