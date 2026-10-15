import UIKit

final class DetailViewController: UIViewController {
    @IBOutlet private weak var messageLabel: UILabel!
    var studentName = "Student"
    var notificationsEnabled = false
    var selectedRole = "Student"
    var preferredEventDate = Date()
    var numberOfGuests = 0
    var announcement: CampusAnnouncement?

    override func viewDidLoad() {
        super.viewDidLoad()
        if let announcement = announcement {
            title = announcement.category
            messageLabel.text = "\(announcement.title)\n\nCategory: \(announcement.category)\nDate: \(announcement.date)\nPriority: \(announcement.priority)\nPosted by: \(announcement.postedBy)"
            return
        }
        title = "Campus Events"
        let notificationStatus = notificationsEnabled ? "on" : "off"
        let dateFormatter = DateFormatter()
        dateFormatter.dateStyle = .medium
        messageLabel.text = "Welcome, \(studentName)!\n\nRole: \(selectedRole)\nNotifications: \(notificationStatus)\nPreferred event date: \(dateFormatter.string(from: preferredEventDate))\nGuests: \(numberOfGuests)"
    }
}
